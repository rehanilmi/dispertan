<?php
namespace App\Support;

use App\Models\{Bantuan, Kelompok, KelompokAnggota};
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Impor Excel format SIMLUHTAN:
 *  1) List Kelompok Tani  : No | Nama Poktan | ID Poktan | Jumlah Anggota | Nama Desa | Nama Ketua | Alamat Sekretariat
 *  2) Daftar Anggota      : blok info (Nama/ID Kelompok Tani, Provinsi, Kabupaten, Kecamatan) + tabel anggota
 * Posisi baris/kolom dideteksi dari teks header, jadi judul/baris kosong di atas tabel tidak masalah.
 */
class KelompokImport
{
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

    private static function num($v): ?float
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v)) return (float) $v;
        $s = str_replace(',', '.', trim((string) $v));
        return is_numeric($s) ? (float) $s : null;
    }

    private static function cariHeader(array $rows, array $wajib): ?array
    {
        foreach (array_slice($rows, 0, 40, true) as $i => $r) {
            $n = array_map(fn ($v) => self::norm($v), $r);
            if (! array_diff($wajib, $n)) return [$i, $r];
        }
        return null;
    }

    private static function gagal(string $judul, string $msg): array
    {
        return ['ok' => false, 'judul' => $judul, 'ringkas' => $msg, 'err' => []];
    }

    /* ---------------- 1) List Kelompok Tani ---------------- */
    public static function poktan(string $path, string $nama): array
    {
        $rows = self::rows($path);
        $h = self::cariHeader($rows, ['namapoktan', 'idpoktan']);
        if (! $h) return self::gagal($nama, 'Header tidak ditemukan. Pastikan ada kolom "Nama Poktan" dan "ID Poktan".');
        [$hi, $hr] = $h;
        $map = [];
        foreach ($hr as $c => $v) { $k = self::norm($v); if ($k !== '' && ! isset($map[$k])) $map[$k] = $c; }
        $g = fn ($r, $k) => isset($map[$k]) ? self::cell($r[$map[$k]] ?? null) : '';

        $baru = $ubah = 0; $err = [];
        foreach ($rows as $i => $r) {
            if ($i <= $hi) continue;
            $id = $g($r, 'idpoktan'); $nm = $g($r, 'namapoktan');
            if ($id === '' && $nm === '') continue;
            if ($id === '' || $nm === '') { $err[] = 'Baris '.($i + 1).': ID Poktan atau Nama Poktan kosong, dilewati.'; continue; }
            $k = Kelompok::updateOrCreate(['id_poktan' => $id], [
                'nama' => $nm, 'jumlah_anggota' => (int) self::num($g($r, 'jumlahanggota')),
                'desa' => $g($r, 'namadesa') ?: null, 'ketua' => $g($r, 'namaketua') ?: null,
                'alamat_sekretariat' => $g($r, 'alamatsekretariat') ?: null,
            ]);
            $k->wasRecentlyCreated ? $baru++ : $ubah++;
        }
        return ['ok' => true, 'judul' => $nama, 'ringkas' => "$baru kelompok baru, $ubah diperbarui, ".count($err).' baris dilewati.', 'err' => $err];
    }

    /* ---------------- 2) Daftar Anggota ---------------- */
    private static function petaAnggota(array $row): array
    {
        $m = []; $kom = 0;
        foreach ($row as $c => $v) {
            $h = self::norm($v);
            if ($h === '') continue;
            if ($h === 'nama') $m['nama'] = $c;
            elseif ($h === 'nik') $m['nik'] = $c;
            elseif (str_contains($h, 'tempat')) $m['ttl'] = $c;
            elseif ($h === 'alamat') $m['alamat'] = $c;
            elseif (str_contains($h, 'jeniskelamin')) $m['jk'] = $c;
            elseif (str_contains($h, 'hp')) $m['hp'] = $c;
            elseif (str_starts_with($h, 'status')) $m['status'] = $c;
            elseif (str_starts_with($h, 'komoditaslain')) $m['komlain'] = $c;
            elseif (str_starts_with($h, 'komoditas')) { $kom = min(3, $kom + 1); $m["kom$kom"] = $c; }
            elseif (str_starts_with($h, 'volume')) $m['vol'.max(1, $kom)] = $c;   // volume mengikuti komoditas sebelumnya
            elseif (str_contains($h, 'luaslahan') && str_contains($h, 'dimiliki')) $m['dimiliki'] = $c;
            elseif (str_contains($h, 'luaslahan')) $m['diusahakan'] = $c;
            elseif (str_contains($h, 'kategori')) $m['kategori'] = $c;
            elseif (str_contains($h, 'koordinat')) $m['koordinat'] = $c;
        }
        return $m;
    }

    public static function anggota(string $path, bool $sinkron, string $nama): array
    {
        $rows = self::rows($path);
        $h = self::cariHeader($rows, ['nama', 'nik']);
        if (! $h) return self::gagal($nama, 'Header tabel anggota (kolom "Nama" dan "NIK") tidak ditemukan.');
        [$hi, $hr] = $h;

        // Blok informasi di atas tabel
        $meta = [];
        foreach (array_slice($rows, 0, $hi) as $r) {
            foreach ($r as $c => $v) {
                $k = self::norm($v);
                if (! in_array($k, ['namakelompoktani', 'idkelompoktani', 'provinsi', 'kabupaten', 'kecamatan'])) continue;
                for ($j = $c + 1; $j < count($r); $j++) {
                    $x = self::cell($r[$j] ?? null);
                    if ($x !== '' && $x !== ':') { $meta[$k] = $x; break; }
                }
            }
        }
        $idp = $meta['idkelompoktani'] ?? '';
        if ($idp === '') return self::gagal($nama, 'Baris "ID Kelompok Tani" tidak ditemukan di bagian atas file.');
        $kel = Kelompok::where('id_poktan', $idp)->first();
        if (! $kel) return self::gagal($nama, "Kelompok dengan ID $idp belum ada. Impor List Kelompok Tani terlebih dahulu.");

        $err = [];
        if (isset($meta['namakelompoktani']) && strcasecmp($meta['namakelompoktani'], $kel->nama) !== 0) {
            $err[] = 'Peringatan: nama di file "'.$meta['namakelompoktani'].'" berbeda dengan database "'.$kel->nama.'". Data tetap diimpor ke ID '.$idp.'.';
        }

        $m = self::petaAnggota($hr);
        $g = fn ($r, $k) => isset($m[$k]) ? self::cell($r[$m[$k]] ?? null) : '';
        $recs = []; $now = now();
        foreach ($rows as $i => $r) {
            if ($i <= $hi) continue;
            $nm = $g($r, 'nama'); $nik = preg_replace('/\s/', '', $g($r, 'nik'));
            if ($nm === '' && $nik === '') continue;
            if (! preg_match('/^\d{16}$/', $nik)) { $err[] = 'Baris '.($i + 1).": NIK \"$nik\" ($nm) tidak valid (harus 16 digit), dilewati."; continue; }
            if ($nm === '') { $err[] = 'Baris '.($i + 1).": nama kosong (NIK $nik), dilewati."; continue; }
            if (isset($recs[$nik])) $err[] = 'Baris '.($i + 1).": NIK $nik muncul lebih dari sekali, data baris ini yang dipakai.";

            // Tempat/Tgl Lahir: "SOLOK, 31-12-1982"
            $tempat = $g($r, 'ttl'); $tgl = null;
            if (preg_match('/^(.*?),?\s*(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})$/', $tempat, $x) && checkdate((int) $x[3], (int) $x[2], (int) $x[4])) {
                $tgl = sprintf('%04d-%02d-%02d', $x[4], $x[3], $x[2]); $tempat = trim($x[1], " ,");
            }
            $jkRaw = strtolower($g($r, 'jk'));
            $jk = str_starts_with($jkRaw, 'p') ? 'P' : (str_starts_with($jkRaw, 'l') ? 'L' : null);
            $hp = preg_replace('/\D/', '', $g($r, 'hp'));
            if (preg_match('/^8\d{8,12}$/', $hp)) $hp = '0'.$hp; elseif (str_starts_with($hp, '62')) $hp = '0'.substr($hp, 2);

            // Koordinat "-1,02279,100,41349,4,4m" => lat -1.02279, lng 100.41349
            $ko = $g($r, 'koordinat'); $lat = $lng = null;
            if (preg_match('/^\s*(-?\d+)[.,](\d+)[.,](\d+)[.,](\d+)/', $ko, $x)) { $lat = (float) "$x[1].$x[2]"; $lng = (float) "$x[3].$x[4]"; }
            elseif (preg_match('/(-?\d+\.\d+)\s*[,; ]\s*(-?\d+\.\d+)/', $ko, $x)) { $lat = (float) $x[1]; $lng = (float) $x[2]; }
            if ($lat !== null && (abs($lat) > 90 || abs($lng) > 180)) $lat = $lng = null;

            $rec = [
                'kelompok_id' => $kel->id, 'nik' => $nik, 'nama' => $nm, 'tempat_lahir' => $tempat ?: null, 'tgl_lahir' => $tgl,
                'alamat' => $g($r, 'alamat') ?: null, 'jk' => $jk, 'no_hp' => substr($hp, 0, 20) ?: null,
                'status_keanggotaan' => $g($r, 'status') ?: null, 'komoditas_lain' => $g($r, 'komlain') ?: null,
                'luas_diusahakan' => self::num($g($r, 'diusahakan')), 'luas_dimiliki' => self::num($g($r, 'dimiliki')),
                'kategori_petani' => $g($r, 'kategori') ?: null, 'koordinat' => $ko ?: null, 'lat' => $lat, 'lng' => $lng,
                'created_at' => $now, 'updated_at' => $now,
            ];
            foreach ([1, 2, 3] as $n) { $rec["komoditas_$n"] = $g($r, "kom$n") ?: null; $rec["volume_$n"] = self::num($g($r, "vol$n")); }
            $recs[$nik] = $rec;
        }
        if (! $recs) return ['ok' => false, 'judul' => $nama, 'ringkas' => 'Tidak ada baris anggota yang valid.', 'err' => $err];

        $niks = array_keys($recs);
        $ada = 0;
        foreach (array_chunk($niks, 1000) as $c) $ada += KelompokAnggota::where('kelompok_id', $kel->id)->whereIn('nik', $c)->count();
        $update = array_values(array_diff(array_keys(reset($recs)), ['kelompok_id', 'nik', 'created_at']));
        foreach (array_chunk(array_values($recs), 200) as $c) KelompokAnggota::upsert($c, ['kelompok_id', 'nik'], $update);

        $hapus = 0;
        if ($sinkron) {
            $hapus = KelompokAnggota::where('kelompok_id', $kel->id)->whereNotIn('nik', $niks)->delete();
        }
        $kel->update([
            'kecamatan' => isset($meta['kecamatan']) ? Kecamatan::norm($meta['kecamatan']) : $kel->kecamatan, 'kabupaten' => $meta['kabupaten'] ?? $kel->kabupaten,
            'provinsi' => $meta['provinsi'] ?? $kel->provinsi,
        ]);
        return ['ok' => true, 'judul' => $nama.' → '.$kel->nama.' ('.$idp.')',
            'ringkas' => (count($niks) - $ada).' anggota baru, '.$ada.' diperbarui'.($sinkron ? ", $hapus dihapus (sinkron)" : '').', '.(count($err)).' catatan.', 'err' => $err];
    }

    /* ---------------- 3) Riwayat Bantuan (format tabel monitoring) ---------------- */
    public static function bantuan(string $path, bool $sinkron, string $nama): array
    {
        $rows = self::rows($path);
        $h = self::cariHeader($rows, ['namapoktan', 'idpoktan']);
        if (! $h) return self::gagal($nama, 'Header tidak ditemukan. Pastikan ada kolom "Nama Poktan" dan "ID Poktan".');
        [$hi, $hr] = $h;
        $isTahun = fn ($v) => preg_match('/^(19|20)\d{2}$/', self::cell($v)) === 1;

        // Baris tahun: sama dengan baris header utama, atau satu baris di atasnya
        $yr = null;
        foreach ([$hi, $hi - 1] as $ri) {
            if ($ri < 0) continue;
            if (count(array_filter($rows[$ri], $isTahun))) { $yr = $ri; break; }
        }
        if ($yr === null) return self::gagal($nama, 'Kolom tahun (mis. 2026, 2025, …) tidak ditemukan pada header.');

        $colYear = []; $cur = null; $awal = [];
        foreach ($rows[$yr] as $c => $v) {
            if ($isTahun($v)) { $cur = (int) self::cell($v); $awal[$cur] = $awal[$cur] ?? $c; }
            if ($cur !== null) $colYear[$c] = $cur;
        }
        $srcRow = $rows[$yr + 1] ?? [];
        $adaSumber = count(array_filter($srcRow, fn ($v) => str_contains(self::norm($v), 'apbd') || str_contains(self::norm($v), 'apbn'))) > 0;
        $kanon = config('layanan.sumber_dana');
        $colSumber = [];
        foreach ($colYear as $c => $y) {
            $n = $adaSumber ? self::norm($srcRow[$c] ?? '') : '';
            $s = $n === '' ? ($adaSumber ? null : ($kanon[($c - $awal[$y]) % 3] ?? null))
                : (str_contains($n, 'provinsi') ? 'APBD Provinsi' : (str_contains($n, 'apbn') ? 'APBN' : (str_contains($n, 'apbd') ? 'APBD Kota' : null)));
            if ($s) $colSumber[$c] = $s;
        }
        if (! $colSumber) return self::gagal($nama, 'Kolom sumber dana (APBD Kota, APBD Provinsi, APBN) tidak terbaca.');
        $mulai = max($hi, $yr, $adaSumber ? $yr + 1 : 0) + 1;

        $map = [];
        foreach ($hr as $c => $v) { $k = self::norm($v); if ($k !== '' && ! isset($map[$k])) $map[$k] = $c; }
        $g = fn ($r, $k) => isset($map[$k]) ? self::cell($r[$map[$k]] ?? null) : '';
        $tahunFile = array_values(array_unique(array_values($colYear)));

        $baru = $ubah = $nBantuan = $hapus = 0; $err = [];
        foreach ($rows as $i => $r) {
            if ($i < $mulai) continue;
            $nm = $g($r, 'namapoktan'); $id = $g($r, 'idpoktan');
            if ($nm === '' && $id === '') continue;
            if ($nm === '') { $err[] = 'Baris '.($i + 1).": nama poktan kosong (ID $id), dilewati."; continue; }

            $kec = Kecamatan::norm($g($r, 'namakecamatan'));
            $attr = array_filter([
                'nama' => $nm, 'kecamatan' => $kec ?: null, 'desa' => $g($r, 'namadesa') ?: null,
                'ketua' => $g($r, 'namaketua') ?: null, 'alamat_sekretariat' => $g($r, 'alamat') ?: null,
            ], fn ($v) => $v !== null);
            $ang = self::num($g($r, 'jumlahanggota'));
            if ($ang !== null) $attr['jumlah_anggota'] = (int) $ang;

            // Cari kelompok: ID Poktan, jika kosong pakai nama + kecamatan
            $k = $id !== '' ? Kelompok::where('id_poktan', $id)->first()
                : Kelompok::whereRaw('lower(nama) = ?', [mb_strtolower($nm)])->when($kec, fn ($q) => $q->where('kecamatan', $kec))->first();
            if ($k) { $k->update($attr); $ubah++; }
            else { $k = Kelompok::create($attr + ['id_poktan' => $id ?: null, 'jumlah_anggota' => $attr['jumlah_anggota'] ?? 0]); $baru++; }

            $set = []; $now = now();
            foreach ($colSumber as $c => $s) {
                $v = self::cell($r[$c] ?? null); $lv = strtolower($v);
                if ($v === '' || $lv === '-' || $lv === '0') continue;
                $set[$colYear[$c].'|'.$s] = ['kelompok_id' => $k->id, 'tahun' => $colYear[$c], 'sumber' => $s, 'keterangan' => mb_substr($v, 0, 500), 'created_at' => $now, 'updated_at' => $now];
            }
            if ($set) { Bantuan::upsert(array_values($set), ['kelompok_id', 'tahun', 'sumber'], ['keterangan', 'updated_at']); $nBantuan += count($set); }
            if ($sinkron) {
                foreach (Bantuan::where('kelompok_id', $k->id)->whereIn('tahun', $tahunFile)->get() as $e) {
                    if (! isset($set[$e->tahun.'|'.$e->sumber])) { $e->delete(); $hapus++; }
                }
            }
        }
        return ['ok' => true, 'judul' => $nama,
            'ringkas' => "$baru kelompok baru, $ubah diperbarui, $nBantuan isian bantuan disimpan".($sinkron ? ", $hapus dihapus (sinkron)" : '').', '.count($err).' baris dilewati.', 'err' => $err];
    }
}
