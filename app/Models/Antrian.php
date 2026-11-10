<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Antrian extends Model
{
    protected $table = 'antrians';
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date', 'dipanggil_at' => 'datetime', 'mulai_at' => 'datetime', 'selesai_at' => 'datetime'];
    public const AKTIF = ['menunggu', 'dipanggil', 'dilayani'];

    public function getNoAttribute(): string { return 'P-'.str_pad($this->nomor, 3, '0', STR_PAD_LEFT); }

    public function scopeHari($q, string $lokasi, $tgl) { return $q->where('lokasi', $lokasi)->whereDate('tanggal', $tgl); }

    /** Ambil nomor berikutnya (dalam transaksi) & tolak bila kuota harian penuh. */
    public static function ambil(array $d): self
    {
        return DB::transaction(function () use ($d) {
            $n = (int) static::hari($d['lokasi'], $d['tanggal'])->lockForUpdate()->max('nomor');
            if ($n >= config('layanan.puskeswan.kuota')) {
                throw ValidationException::withMessages(['tanggal' => 'Kuota antrian pada tanggal tersebut sudah penuh. Silakan pilih tanggal lain.']);
            }
            return static::create($d + ['nomor' => $n + 1, 'kode' => Str::upper(Str::random(8))]);
        });
    }

    /** Rata-rata durasi layanan hari ini (menit), minimal 5, default 10. */
    public static function rataLayanan(string $lokasi): int
    {
        $m = static::hari($lokasi, today())->whereNotNull('mulai_at')->whereNotNull('selesai_at')->get()
            ->avg(fn ($a) => $a->mulai_at->diffInMinutes($a->selesai_at));
        return max(5, (int) round($m ?: 10));
    }

    public function info(): array
    {
        $q = static::hari($this->lokasi, $this->tanggal);
        $depan = (clone $q)->whereIn('status', self::AKTIF)->where('nomor', '<', $this->nomor)->count();
        $now = (clone $q)->whereIn('status', ['dipanggil', 'dilayani'])->orderByDesc('dipanggil_at')->first();
        return ['depan' => $depan, 'sekarang' => $now?->no, 'estimasi' => $depan * static::rataLayanan($this->lokasi)];
    }

    public static function papan(string $lokasi): array
    {
        $q = static::hari($lokasi, today());
        $now = (clone $q)->whereIn('status', ['dipanggil', 'dilayani'])->orderByDesc('dipanggil_at')->first();
        return [
            'sekarang' => $now?->no, 'menunggu' => (clone $q)->where('status', 'menunggu')->count(),
            'selesai' => (clone $q)->where('status', 'selesai')->count(), 'total' => (clone $q)->count(),
        ];
    }
}
