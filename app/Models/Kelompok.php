<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    protected $table = 'kelompoks';
    protected $guarded = [];

    public function anggotas() { return $this->hasMany(KelompokAnggota::class); }
    public function bantuans() { return $this->hasMany(Bantuan::class); }

    // Status analisis (dihitung dari riwayat bantuan, selalu mutakhir)
    public function getPernahDibantuAttribute(): bool { return $this->bantuans->isNotEmpty(); }
    public function getJumlahTahunDibantuAttribute(): int { return $this->bantuans->pluck('tahun')->unique()->count(); }
    public function getTahunTerakhirDibantuAttribute(): ?int { return $this->bantuans->max('tahun'); }
}
