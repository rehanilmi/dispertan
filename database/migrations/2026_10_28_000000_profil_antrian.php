<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Biodata pegawai (halaman Profil Saya)
        Schema::table('users', function (Blueprint $t) {
            $t->string('nip', 30)->nullable(); $t->string('jabatan', 100)->nullable(); $t->string('no_hp', 30)->nullable();
            $t->text('alamat')->nullable(); $t->string('foto')->nullable();
        });
        // Status antrian: Terdaftar | Menunggu | Sedang diperiksa | Selesai ("Sedang diperiksa" = 16 karakter)
        Schema::table('pendaftarans', fn (Blueprint $t) => $t->string('status_pelayanan', 20)->default('Menunggu')->change());
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['nip', 'jabatan', 'no_hp', 'alamat', 'foto']));
    }
};
