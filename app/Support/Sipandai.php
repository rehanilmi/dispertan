<?php
namespace App\Support;

use App\Models\Pendaftaran;

class Sipandai
{
    private const KOLOM = ['umum', 'rabies', 'oh', 'kastrasi', 'jahit', 'operasi', 'skkh'];

    /** Setara getDashboardData() di Code.gs: semua agregasi dalam satu permintaan. */
    public static function rekap(): array
    {
        $tz = config('sipandai.timezone');
        $now = now($tz);
        $nol = fn (array $k) => array_fill_keys($k, 0);

        $hitung = fn ($q, string $kol, array $kunci) => array_replace($nol($kunci),
            array_intersect_key($q->selectRaw("$kol as k, count(*) as n")->groupBy($kol)->pluck('n', 'k')->map(fn ($v) => (int) $v)->all(), $nol($kunci)));

        $awalBulan = $now->copy()->startOfMonth();
        $harian = Pendaftaran::where('tanggal', '>=', $now->copy()->subDays(13)->toDateString())
            ->selectRaw('tanggal as k, count(*) as n')->groupBy('tanggal')->pluck('n', 'k')->all();
        $bulanan = Pendaftaran::where('tanggal', '>=', $awalBulan->copy()->subMonths(5)->toDateString())
            ->selectRaw("date_format(tanggal,'%Y-%m') as k, count(*) as n")->groupBy('k')->pluck('n', 'k')->all();

        return [
            'success' => true,
            'totalHariIni' => Pendaftaran::whereDate('tanggal', $now->toDateString())->count(),
            'totalBulanIni' => Pendaftaran::where('tanggal', '>=', $awalBulan->toDateString())->where('tanggal', '<=', $now->copy()->endOfMonth()->toDateString())->count(),
            'totalKeseluruhan' => Pendaftaran::count(),
            'perLayananSemua' => $hitung(Pendaftaran::query(), 'jenis_pelayanan', config('sipandai.pelayanan')),
            'perLayananBulanIni' => $hitung(Pendaftaran::where('tanggal', '>=', $awalBulan->toDateString()), 'jenis_pelayanan', config('sipandai.pelayanan')),
            'perHewanSemua' => $hitung(Pendaftaran::query(), 'jenis_hewan', config('sipandai.hewan')),
            'trendHarian' => collect(range(13, 0))->map(fn ($i) => ['tanggal' => ($d = $now->copy()->subDays($i)->toDateString()), 'jumlah' => (int) ($harian[$d] ?? 0)])->all(),
            'trendBulanan' => collect(range(5, 0))->map(fn ($i) => ['bulan' => ($b = $awalBulan->copy()->subMonths($i)->format('Y-m')), 'jumlah' => (int) ($bulanan[$b] ?? 0)])->all(),
            'updatedAt' => $now->toIso8601String(),
        ];
    }

    /**
     * Rekap pelayanan per bulan (tabel laporan). Tiap pendaftaran masuk TEPAT SATU kolom, urutan aturan:
     *  SKKH -> Vaksinasi Rabies -> Sterilisasi (OH untuk betina / Kastrasi untuk jantan)
     *  -> Operasi Lainnya (tindakan dokter memuat bedah/operasi) -> Penjahitan Luka (tindakan memuat jahit) -> Pelayanan Umum.
     */
    public static function rekapBulanan(int $tahun): array
    {
        $nol = fn () => array_fill_keys([...self::KOLOM, 'total'], 0);
        $bln = array_fill(1, 12, $nol());
        $skkh = config('sipandai.skkh');
        $rows = Pendaftaran::whereBetween('tanggal', ["$tahun-01-01", "$tahun-12-31"])->select('tanggal', 'jenis_pelayanan', 'jenis_kelamin', 'jenis_tindakan')->cursor();
        foreach ($rows as $p) {
            $t = mb_strtolower((string) $p->jenis_tindakan);
            $k = match (true) {
                $p->jenis_pelayanan === $skkh => 'skkh',
                $p->jenis_pelayanan === 'Vaksinasi' => 'rabies',
                $p->jenis_pelayanan === 'Sterilisasi' => $p->jenis_kelamin === 'Betina' ? 'oh' : 'kastrasi',
                str_contains($t, 'bedah') || str_contains($t, 'operasi') => 'operasi',
                str_contains($t, 'jahit') => 'jahit',
                default => 'umum',
            };
            $m = (int) $p->tanggal->format('n');
            $bln[$m][$k]++; $bln[$m]['total']++;
        }
        $tot = $nol();
        $baris = [];
        foreach ($bln as $m => $v) {
            foreach ($v as $k => $n) $tot[$k] += $n;
            $baris[] = ['no' => $m, 'bulan' => \Illuminate\Support\Carbon::create($tahun, $m, 1)->locale(app()->getLocale())->translatedFormat('F')] + $v;
        }
        $awal = (int) substr((string) (Pendaftaran::min('tanggal') ?: date('Y')), 0, 4);
        return ['success' => true, 'tahun' => $tahun, 'tahunList' => range(max((int) date('Y'), $tahun), min($awal, $tahun)), 'baris' => $baris, 'total' => $tot];
    }
}
