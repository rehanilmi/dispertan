<?php
namespace App\Support;

use App\Models\Pendaftaran;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Impor pasien lama dari Excel (kolom = HEADER_PENDAFTARAN): No_Pendaftaran, Urutan_Hewan, Tanggal, Waktu, Nama_Pemilik, NIK, Alamat, No_HP,
 * Nama_Hewan, Jenis_Hewan, Ras, Jenis_Kelamin, Umur_TglLahir, Ciri_Khusus, Jenis_Pelayanan, Keluhan_Utama, Info_Tambahan, SKKH_*, Status_Pelayanan,
 * Diagnosa, Jenis_Tindakan, Catatan_Pemeriksaan, Petugas_Update, Waktu_Update. Kunci data = (No_Pendaftaran, Urutan_Hewan): impor ulang memperbarui, tidak menggandakan.
 */
class PendaftaranImport
{
    public const KOLOM = ['ID_Pendaftaran', 'No_Pendaftaran', 'Urutan_Hewan', 'Tanggal', 'Waktu', 'Nama_Pemilik', 'NIK', 'Alamat', 'No_HP', 'Nama_Hewan', 'Jenis_Hewan', 'Ras', 'Jenis_Kelamin',
        'Umur_TglLahir', 'Ciri_Khusus', 'Jenis_Pelayanan', 'Keluhan_Utama', 'Info_Tambahan', 'SKKH_Tanggal', 'SKKH_Nama_Penerima', 'SKKH_Alamat_Penerima', 'SKKH_Jumlah_Jantan',
        'SKKH_Jumlah_Betina', 'Status_Pelayanan', 'Diagnosa', 'Jenis_Tindakan', 'Catatan_Pemeriksaan', 'Petugas_Update', 'Waktu_Update'];

    private static function rows(string $path): array
    {
        $r = IOFactory::createReaderForFile($path);
        $r->setReadDataOnly(true);
        return $r->load($path)->getActiveSheet()->toArray(null, false, false, false);
    }

    private static function norm($v): string { return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $v))); }

    private static function cell($v): string
    {
        if ($v === null) return '';
        if (is_int($v) || is_float($v)) return rtrim(rtrim(number_format((float) $v, 10, '.', ''), '0'), '.');
        return trim((string) $v);
    }

    private static function parse(string $s, array $formats): ?\DateTime
    {
        foreach ($formats as $f) {
            $d = \DateTime::createFromFormat('!'.$f, $s);
            $e = \DateTime::getLastErrors();
            if ($d && (! $e || ($e['warning_count'] === 0 && $e['error_count'] === 0))) return $d;
        }
        return null;
    }

    private static function tgl($v): ?string
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v) && (float) $v > 20000) return Date::excelToDateTimeObject((float) $v)->format('Y-m-d');
        return self::parse(trim((string) $v), ['Y-m-d H:i:s', 'Y-m-d', 'd/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'd-m-Y H:i:s', 'd-m-Y', 'd.m.Y'])?->format('Y-m-d');
    }

    private static function dt($v): ?string
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v) && (float) $v > 20000) return Date::excelToDateTimeObject((float) $v)->format('Y-m-d H:i:s');
        return self::parse(trim((string) $v), ['Y-m-d H:i:s', 'Y-m-d H:i', 'd/m/Y H:i:s', 'd/m/Y H:i', 'd-m-Y H:i:s', 'd-m-Y H:i', 'd/m/Y', 'Y-m-d'])?->format('Y-m-d H:i:s');
    }

    private static function waktu($v): string
    {
        if ($v === null || $v === '') return '00:00:00';
        if (is_numeric($v) && (float) $v < 1) return gmdate('H:i:s', (int) round((float) $v * 86400));
        if (is_numeric($v) && (float) $v > 20000) return Date::excelToDateTimeObject((float) $v)->format('H:i:s');
        if (preg_match('/(\d{1,2}):(\d{2})(?::(\d{2}))?/', (string) $v, $m)) return sprintf('%02d:%02d:%02d', $m[1], $m[2], $m[3] ?? 0);
        return '00:00:00';
    }

    private static function cocok(string $v, array $list): ?string
    {
        foreach ($list as $x) if (mb_strtolower($x) === mb_strtolower($v)) return $x;
        return null;
    }

    public static function jalankan(string $path, string $nama): array
    {
        $c = config('sipandai');
        $rows = self::rows($path);
        $hi = null; $map = [];
        foreach (array_slice($rows, 0, 20, true) as $i => $r) {
            $n = array_map(fn ($v) => self::norm($v), $r);
            if (in_array('nopendaftaran', $n, true) && in_array('namapemilik', $n, true)) {
                $hi = $i;
                foreach ($n as $col => $k) if ($k !== '' && ! isset($map[$k])) $map[$k] = $col;
                break;
            }
        }
        if ($hi === null) return ['ok' => false, 'judul' => $nama, 'ringkas' => 'Header tidak ditemukan. Pastikan ada kolom "No_Pendaftaran" dan "Nama_Pemilik".', 'err' => []];

        $g = fn ($r, $k) => isset($map[$k]) ? self::cell($r[$map[$k]] ?? null) : '';
        $raw = fn ($r, $k) => isset($map[$k]) ? ($r[$map[$k]] ?? null) : null;
        $nul = fn ($s) => $s === '' ? null : $s;
        $baru = $ubah = $lewat = 0; $err = [];

        foreach ($rows as $i => $r) {
            if ($i <= $hi) continue;
            $no = $g($r, 'nopendaftaran'); $pm = $g($r, 'namapemilik'); $nm = $g($r, 'namahewan');
            if ($no === '' && $pm === '' && $nm === '') continue;
            $b = $i + 1;
            if ($no === '') { $err[] = "Baris $b: No_Pendaftaran kosong, dilewati."; $lewat++; continue; }
            if ($pm === '' || $nm === '') { $err[] = "Baris $b ($no): Nama_Pemilik atau Nama_Hewan kosong, dilewati."; $lewat++; continue; }

            $tanggal = self::tgl($raw($r, 'tanggal'));
            if (! $tanggal && preg_match('/(\d{4})(\d{2})(\d{2})/', $no, $m) && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) $tanggal = "$m[1]-$m[2]-$m[3]";   // dari nomor REG
            if (! $tanggal) { $err[] = "Baris $b ($no): tanggal tidak terbaca, dilewati."; $lewat++; continue; }

            $urut = $g($r, 'urutanhewan') ?: '1/1';
            $hewan = self::cocok($g($r, 'jenishewan'), $c['hewan']);
            if ($hewan === null) { $err[] = "Baris $b ($no): jenis hewan \"".$g($r, 'jenishewan').'" tidak dikenal, disimpan apa adanya.'; $hewan = $g($r, 'jenishewan') ?: 'Lainnya'; }
            $pel = self::cocok($g($r, 'jenispelayanan'), $c['pelayanan']);
            if ($pel === null) { $err[] = "Baris $b ($no): jenis pelayanan \"".$g($r, 'jenispelayanan').'" tidak dikenal, disimpan apa adanya.'; $pel = $g($r, 'jenispelayanan') ?: '-'; }
            $jkr = mb_strtolower($g($r, 'jeniskelamin'));
            $jk = str_starts_with($jkr, 'j') ? 'Jantan' : (str_starts_with($jkr, 'b') ? 'Betina' : '-');
            $nik = preg_replace('/\D/', '', $g($r, 'nik'));
            if (strlen($nik) !== 16) $err[] = "Baris $b ($no): NIK \"$nik\" bukan 16 digit (tetap diimpor).";
            $hp = preg_replace('/[^\d+]/', '', $g($r, 'nohp'));
            if (preg_match('/^8\d{8,12}$/', $hp)) $hp = '0'.$hp;   // angka 0 di depan sering hilang di Excel
            $status = self::cocok($g($r, 'statuspelayanan'), Pendaftar::STATUS) ?? ($g($r, 'diagnosa') !== '' ? 'Selesai' : 'Menunggu');

            $attr = [
                'tanggal' => $tanggal, 'waktu' => self::waktu($raw($r, 'waktu')),
                'nama_pemilik' => mb_substr($pm, 0, 100), 'nik' => substr($nik, 0, 16), 'alamat' => $g($r, 'alamat') ?: '-', 'no_hp' => substr($hp, 0, 20),
                'nama_hewan' => mb_substr($nm, 0, 100), 'jenis_hewan' => mb_substr($hewan, 0, 30), 'ras' => $nul(mb_substr($g($r, 'ras'), 0, 100)),
                'jenis_kelamin' => $jk, 'umur_tgl_lahir' => mb_substr($g($r, 'umurtgllahir') ?: '-', 0, 60), 'ciri_khusus' => $nul(mb_substr($g($r, 'cirikhusus'), 0, 200)),
                'jenis_pelayanan' => mb_substr($pel, 0, 60), 'keluhan_utama' => $g($r, 'keluhanutama') ?: '-', 'info_tambahan' => $nul($g($r, 'infotambahan')),
                'skkh_tanggal' => self::tgl($raw($r, 'skkhtanggal')), 'skkh_nama_penerima' => $nul($g($r, 'skkhnamapenerima')), 'skkh_alamat_penerima' => $nul($g($r, 'skkhalamatpenerima')),
                'skkh_jumlah_jantan' => $g($r, 'skkhjumlahjantan') === '' ? null : (int) $g($r, 'skkhjumlahjantan'),
                'skkh_jumlah_betina' => $g($r, 'skkhjumlahbetina') === '' ? null : (int) $g($r, 'skkhjumlahbetina'),
                'status_pelayanan' => $status, 'diagnosa' => $nul($g($r, 'diagnosa')), 'jenis_tindakan' => $nul($g($r, 'jenistindakan')),
                'catatan_pemeriksaan' => $nul($g($r, 'catatanpemeriksaan')), 'petugas_update' => $nul(mb_substr($g($r, 'petugasupdate'), 0, 250)),
                'waktu_update' => self::dt($raw($r, 'waktuupdate')),
            ];
            $p = Pendaftaran::where(['no_pendaftaran' => $no, 'urutan_hewan' => $urut])->first();
            if ($p) { $p->forceFill($attr)->save(); $ubah++; }
            else { Pendaftaran::create($attr + ['uid' => Str::upper(Str::random(8)), 'no_pendaftaran' => $no, 'urutan_hewan' => $urut]); $baru++; }
        }
        return ['ok' => true, 'judul' => $nama, 'ringkas' => "$baru pendaftaran baru, $ubah diperbarui, $lewat baris dilewati.", 'err' => $err];
    }
}
