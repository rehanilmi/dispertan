<?php
namespace App\Support;

use App\Models\{Bantuan, Kelompok};
use PhpOffice\PhpSpreadsheet\{IOFactory, Spreadsheet};
use PhpOffice\PhpSpreadsheet\Cell\{Coordinate, DataType};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** Port dari getDashboardData() & buildSummary_() di Google Apps Script. */
class Monitoring
{
    public static function data(bool $linkUbah = false): array
    {
        $awal = (int) config('layanan.tahun_awal', 2020);
        $min = (int) (Bantuan::min('tahun') ?: $awal);
        $max = max((int) date('Y'), (int) Bantuan::max('tahun'));
        $years = range(min($awal, $min), $max);   // otomatis bertambah tiap tahun

        $groups = Kelompok::with('bantuans')->orderBy('kecamatan')->orderBy('nama')->get()->map(function ($k, $i) use ($years, $linkUbah) {
            $by = $k->bantuans->groupBy('tahun');
            $b = [];
            foreach ($years as $y) {
                $d = $by->get($y, collect());
                $b[$y] = ['ada' => $d->isNotEmpty(), 'detail' => $d->map(fn ($x) => ['sumber' => $x->sumber, 'keterangan' => $x->keterangan])->values()->all()];
            }
            return [
                'id' => $k->id, 'no' => $i + 1, 'kecamatan' => $k->kecamatan ?: 'Tanpa Kecamatan', 'poktan' => $k->nama,
                'idPoktan' => $k->id_poktan ?: '-', 'jumlahAnggota' => (int) $k->jumlah_anggota, 'desa' => $k->desa ?: '-',
                'ketua' => $k->ketua ?: '-', 'alamat' => $k->alamat_sekretariat ?: '-', 'bantuan' => $b,
                'pernahDibantu' => $k->pernah_dibantu, 'jumlahTahunDibantu' => $k->jumlah_tahun_dibantu,
                'tahunTerakhirDibantu' => $k->tahun_terakhir_dibantu,
                'editUrl' => $linkUbah ? route('admin.kelompok.show', $k->id) : null,
            ];
        })->values()->all();

        return ['years' => $years, 'groups' => $groups, 'summary' => self::ringkas($groups, $years), 'generatedAt' => now()->toIso8601String(),
            'batasLama' => (int) date('Y') - 3];
    }

    private static function ringkas(array $groups, array $years): array
    {
        $total = count($groups);
        $sudah = count(array_filter($groups, fn ($g) => $g['pernahDibantu']));
        $per = [];
        foreach ($groups as $g) {
            $k = $g['kecamatan'];
            $per[$k] ??= ['kecamatan' => $k, 'total' => 0, 'sudah' => 0, 'belum' => 0];
            $per[$k]['total']++;
            $per[$k][$g['pernahDibantu'] ? 'sudah' : 'belum']++;
        }
        $per = array_values($per);
        usort($per, fn ($a, $b) => $b['total'] <=> $a['total']);
        return [
            'totalKelompok' => $total, 'totalSudahDibantu' => $sudah, 'totalBelumDibantu' => $total - $sudah,
            'persenSudah' => $total ? round($sudah / $total * 100, 1) : 0, 'perKecamatan' => $per, 'jumlahKecamatan' => count($per),
            'perTahun' => array_map(fn ($y) => ['tahun' => $y, 'jumlah' => count(array_filter($groups, fn ($g) => $g['bantuan'][$y]['ada']))], $years),
        ];
    }

    public static function filter(array $groups, array $f): array
    {
        $q = mb_strtolower(trim($f['q'] ?? '')); $kc = $f['kecamatan'] ?? ''; $st = $f['status'] ?? ''; $th = $f['tahun'] ?? '';
        return array_values(array_filter($groups, fn ($g) => (! $kc || $g['kecamatan'] === $kc)
            && (! $q || str_contains(mb_strtolower($g['poktan']), $q))
            && (! $th || ! empty($g['bantuan'][(int) $th]['ada']))
            && ($st !== 'sudah' || $g['pernahDibantu']) && ($st !== 'belum' || ! $g['pernahDibantu'])));
    }

    /** Tulis format tabel yang sama dengan format impor (2 baris header: tahun lalu sumber dana). */
    public static function tulis(Worksheet $ws, array $groups, array $years): void
    {
        $lbl = ['APBD Kota' => 'APBD Kota Padang', 'APBD Provinsi' => 'APBD Provinsi', 'APBN' => 'APBN'];
        $src = config('layanan.sumber_dana');
        rsort($years);
        $ws->fromArray(['No.', 'Nama Kecamatan', 'Nama Poktan', 'ID Poktan', 'Jumlah Anggota', 'Nama Desa', 'Nama Ketua', 'Alamat'], null, 'A1');
        $c = 9; $kolom = [];
        foreach ($years as $y) {
            $a = Coordinate::stringFromColumnIndex($c); $b = Coordinate::stringFromColumnIndex($c + 2);
            $ws->setCellValueExplicit($a.'1', (string) $y, DataType::TYPE_STRING); $ws->mergeCells("{$a}1:{$b}1");
            foreach ($src as $j => $s) { $ws->setCellValue(Coordinate::stringFromColumnIndex($c + $j).'2', $lbl[$s]); $kolom[$y][$s] = Coordinate::stringFromColumnIndex($c + $j); }
            $c += 3;
        }
        foreach (array_values($groups) as $i => $g) {
            $r = 3 + $i;
            $ws->setCellValue("A$r", $i + 1); $ws->setCellValue("B$r", $g['kecamatan']); $ws->setCellValue("C$r", $g['poktan']);
            if (($g['idPoktan'] ?? '-') !== '-') $ws->setCellValueExplicit("D$r", (string) $g['idPoktan'], DataType::TYPE_STRING);
            $ws->setCellValue("E$r", $g['jumlahAnggota']); $ws->setCellValue("F$r", $g['desa']); $ws->setCellValue("G$r", $g['ketua']); $ws->setCellValue("H$r", $g['alamat']);
            foreach ($years as $y) foreach (($g['bantuan'][$y]['detail'] ?? []) as $d) {
                if (isset($kolom[$y][$d['sumber']])) $ws->setCellValue($kolom[$y][$d['sumber']].$r, $d['keterangan']);
            }
        }
    }

    public static function stream(string $nama, array $groups, array $years)
    {
        $ss = new Spreadsheet();
        self::tulis($ss->getActiveSheet(), $groups, $years);
        return response()->streamDownload(fn () => IOFactory::createWriter($ss, 'Xlsx')->save('php://output'), $nama,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
