<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel lama (pendaftaran publik) diganti skema yang mengikuti data SIMLUHTAN.
        Schema::dropIfExists('kelompoks');
        Schema::create('kelompoks', function (Blueprint $t) {
            $t->id();
            $t->string('id_poktan', 20)->unique();
            $t->string('nama')->index();
            $t->unsignedInteger('jumlah_anggota')->default(0);
            $t->string('desa')->nullable()->index();
            $t->string('ketua')->nullable();
            $t->text('alamat_sekretariat')->nullable();
            $t->string('kecamatan')->nullable()->index();
            $t->string('kabupaten')->nullable();
            $t->string('provinsi')->nullable();
            $t->timestamps();
        });
        Schema::create('kelompok_anggotas', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('kelompok_id')->index();
            $t->string('nik', 16);
            $t->string('nama');
            $t->string('tempat_lahir')->nullable(); $t->date('tgl_lahir')->nullable();
            $t->text('alamat')->nullable(); $t->string('jk', 1)->nullable(); $t->string('no_hp', 20)->nullable();
            $t->string('status_keanggotaan')->nullable();
            foreach ([1, 2, 3] as $i) { $t->string("komoditas_$i")->nullable(); $t->decimal("volume_$i", 12, 2)->nullable(); }
            $t->string('komoditas_lain')->nullable();
            $t->decimal('luas_diusahakan', 12, 4)->nullable(); $t->decimal('luas_dimiliki', 12, 4)->nullable();
            $t->string('kategori_petani')->nullable();
            $t->string('koordinat')->nullable(); $t->decimal('lat', 9, 6)->nullable(); $t->decimal('lng', 9, 6)->nullable();
            $t->timestamps();
            $t->unique(['kelompok_id', 'nik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_anggotas');
        Schema::dropIfExists('kelompoks');
    }
};
