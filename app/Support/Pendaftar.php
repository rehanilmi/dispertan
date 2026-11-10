<?php
namespace App\Support;

use App\Models\Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Logika pendaftaran SIPANDAI yang dipakai formulir publik dan formulir petugas (admin). */
class Pendaftar
{
    public const STATUS = ['Terdaftar', 'Menunggu', 'Sedang diperiksa', 'Selesai'];

    private static function t($v): string { return trim(strip_tags((string) ($v ?? ''))); }   // cleanText_

    /** Port validatePemilik_ + validateHewan_ (pesan sama dengan Code.gs). Mengembalikan pesan galat atau null. */
    public static function cek(array $d): ?string
    {
        $c = config('sipandai');
        $p = $d['pemilik'] ?? null;
        if (! is_array($p)) return __('Data pemilik tidak ditemukan.');
        $h = $d['hewan'] ?? null;
        if (! is_array($h) || ! $h) return __('Minimal 1 data hewan harus diisi.');
        if (count($h) > $c['maks_hewan']) return __('Maksimal :n hewan dalam satu pendaftaran.', ['n' => $c['maks_hewan']]);

        if (self::t($p['nama_pemilik'] ?? '') === '') return __('Nama pemilik wajib diisi.');
        if (! preg_match('/^\d{16}$/', self::t($p['nik'] ?? ''))) return __('NIK harus terdiri dari 16 digit angka.');
        if (self::t($p['alamat'] ?? '') === '') return __('Alamat wajib diisi.');
        if (! preg_match('/^(\+62|62|0)8\d{7,12}$/', preg_replace('/[\s-]/', '', self::t($p['no_hp'] ?? '')))) return __('Format nomor HP tidak valid.');

        foreach (array_values($h) as $i => $x) {
            $e = self::cekHewan(is_array($x) ? $x : [], $c);
            if ($e) return __('Hewan ke-:n: :pesan', ['n' => $i + 1, 'pesan' => $e]);
        }
        return null;
    }

    private static function cekHewan(array $h, array $c): ?string
    {
        if (self::t($h['nama_hewan'] ?? '') === '') return __('Nama hewan wajib diisi.');
        if (! in_array($h['jenis_hewan'] ?? null, $c['hewan'], true)) return __('Jenis hewan tidak valid.');
        if (! in_array($h['jenis_kelamin'] ?? null, ['Jantan', 'Betina'], true)) return __('Jenis kelamin hewan wajib dipilih.');
        if (self::t($h['umur_tgl_lahir'] ?? '') === '') return __('Umur atau tanggal lahir wajib diisi.');
        if (! in_array($h['jenis_pelayanan'] ?? null, $c['pelayanan'], true)) return __('Jenis pelayanan tidak valid.');
        if (self::t($h['keluhan_utama'] ?? '') === '') return __('Keluhan utama wajib diisi.');
        if (($h['jenis_pelayanan'] ?? '') === $c['skkh']) {
            if (self::t($h['skkh_tanggal'] ?? '') === '' || ! strtotime((string) $h['skkh_tanggal'])) return __('Hari/Tanggal SKKH wajib diisi.');
            if (self::t($h['skkh_nama_penerima'] ?? '') === '') return __('Nama penerima wajib diisi untuk SKKH.');
            if (self::t($h['skkh_alamat_penerima'] ?? '') === '') return __('Alamat penerima wajib diisi untuk SKKH.');
            $j = (int) ($h['skkh_jumlah_jantan'] ?? 0); $b = (int) ($h['skkh_jumlah_betina'] ?? 0);
            if ($j < 0 || $b < 0) return __('Jumlah hewan jantan/betina tidak boleh negatif.');
            if ($j + $b <= 0) return __('Jumlah hewan jantan atau betina wajib diisi minimal 1.');
        }
        return null;
    }

    /** Simpan (1 nomor, N baris hewan) dalam transaksi; ulangi bila nomor bentrok. Mengembalikan data struk. */
    public static function simpan(array $d, string $status = 'Terdaftar'): array
    {
        $c = config('sipandai'); $p = $d['pemilik']; $now = now($c['timezone']);
        $hewan = array_values($d['hewan']); $total = count($hewan);

        $no = null;
        for ($try = 0; $try < 3 && ! $no; $try++) {
            try {
                $no = DB::transaction(function () use ($p, $hewan, $total, $now, $c, $status) {
                    $no = Pendaftaran::nomorBerikut($now);
                    foreach ($hewan as $i => $h) {
                        $skkh = ($h['jenis_pelayanan'] ?? '') === $c['skkh'];
                        Pendaftaran::create([
                            'uid' => Str::upper(Str::random(8)), 'no_pendaftaran' => $no, 'urutan_hewan' => ($i + 1).'/'.$total,
                            'tanggal' => $now->toDateString(), 'waktu' => $now->format('H:i:s'),
                            'nama_pemilik' => self::t($p['nama_pemilik']), 'nik' => self::t($p['nik']), 'alamat' => self::t($p['alamat']), 'no_hp' => self::t($p['no_hp']),
                            'nama_hewan' => self::t($h['nama_hewan']), 'jenis_hewan' => $h['jenis_hewan'], 'ras' => self::t($h['ras'] ?? '') ?: null,
                            'jenis_kelamin' => $h['jenis_kelamin'], 'umur_tgl_lahir' => self::t($h['umur_tgl_lahir']), 'ciri_khusus' => self::t($h['ciri_khusus'] ?? '') ?: null,
                            'jenis_pelayanan' => $h['jenis_pelayanan'], 'keluhan_utama' => self::t($h['keluhan_utama']), 'info_tambahan' => self::t($h['info_tambahan'] ?? '') ?: null,
                            'skkh_tanggal' => $skkh ? date('Y-m-d', strtotime($h['skkh_tanggal'])) : null,
                            'skkh_nama_penerima' => $skkh ? self::t($h['skkh_nama_penerima']) : null,
                            'skkh_alamat_penerima' => $skkh ? self::t($h['skkh_alamat_penerima']) : null,
                            'skkh_jumlah_jantan' => $skkh ? (int) ($h['skkh_jumlah_jantan'] ?? 0) : null,
                            'skkh_jumlah_betina' => $skkh ? (int) ($h['skkh_jumlah_betina'] ?? 0) : null,
                            'status_pelayanan' => $status,
                        ]);
                    }
                    return $no;
                });
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() !== '23000' || $try === 2) throw $e;   // 23000 = nomor bentrok dengan pendaftar lain -> ulangi
            }
        }

        $nik = self::t($p['nik']);
        return [
            'noPendaftaran' => $no, 'jumlahHewan' => $total,
            'tanggal' => $now->locale(app()->getLocale())->translatedFormat('l, d F Y'), 'waktu' => $now->format('H:i').' WIB',
            'lokasi' => $c['lokasi'], 'pemilik' => self::t($p['nama_pemilik']), 'nikSamar' => substr($nik, 0, 4).str_repeat('•', 8).substr($nik, -4),
            'hewan' => array_map(fn ($h, $i) => ['urut' => ($i + 1).'/'.$total, 'nama' => self::t($h['nama_hewan']), 'jenis' => __($h['jenis_hewan']), 'pelayanan' => __($h['jenis_pelayanan'])], $hewan, array_keys($hewan)),
        ];
    }

    /** "Lira Mofa Arhas" -> "Lira M. A." (nama pemilik di halaman publik disamarkan) */
    public static function samar(string $nama): string
    {
        $w = preg_split('/\s+/', trim($nama)) ?: [];
        $out = [array_shift($w)];
        foreach ($w as $x) $out[] = mb_strtoupper(mb_substr($x, 0, 1)).'.';
        return implode(' ', array_filter($out));
    }

    /** Antrean hari ini (urut nomor pendaftaran). $publik=true: tanpa uid & nama pemilik disamarkan. */
    public static function antrianHariIni(bool $publik): array
    {
        $tz = config('sipandai.timezone'); $hari = now($tz)->toDateString();
        $rows = Pendaftaran::whereDate('tanggal', $hari)->orderBy('id')
            ->get(['uid', 'no_pendaftaran', 'urutan_hewan', 'nama_hewan', 'jenis_hewan', 'nama_pemilik', 'jenis_pelayanan', 'waktu', 'status_pelayanan']);
        $ringkas = array_fill_keys(self::STATUS, 0);
        $baris = $rows->map(function ($p) use ($publik, &$ringkas) {
            $s = in_array($p->status_pelayanan, self::STATUS, true) ? $p->status_pelayanan : 'Menunggu';
            $ringkas[$s]++;
            return ['uid' => $publik ? null : $p->uid, 'no' => $p->no_pendaftaran, 'urutan' => $p->urutan_hewan, 'hewan' => $p->nama_hewan, 'jenis' => __($p->jenis_hewan),
                'pemilik' => $publik ? self::samar($p->nama_pemilik) : $p->nama_pemilik, 'layanan' => __($p->jenis_pelayanan),
                'waktu' => substr((string) $p->waktu, 0, 5), 'status' => $s, 'label' => __($s)];
        })->values()->all();
        return ['tanggal' => now($tz)->locale(app()->getLocale())->translatedFormat('l, d F Y'), 'ringkas' => $ringkas, 'rows' => $baris, 'diperbarui' => now($tz)->format('H:i:s')];
    }
}
