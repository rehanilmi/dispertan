<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->unsignedBigInteger('unit_id')->nullable());

        Schema::create('bank_dokumens', function (Blueprint $t) {
            $t->id(); $t->string('kategori', 40)->index(); $t->unsignedSmallInteger('tahun')->index();
            $t->string('varian', 20)->default('murni'); $t->unsignedTinyInteger('nomor')->default(0);
            $t->string('judul'); $t->text('deskripsi')->nullable();
            $t->string('akses', 10)->default('publik')->index();
            $t->unsignedBigInteger('unit_id')->nullable()->index(); $t->unsignedBigInteger('user_id')->nullable();
            $t->timestamps();
        });
        Schema::create('bank_dokumen_versis', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('bank_dokumen_id')->index(); $t->unsignedSmallInteger('versi');
            $t->string('file'); $t->string('nama_asli'); $t->unsignedBigInteger('ukuran')->default(0);
            $t->string('catatan')->nullable(); $t->unsignedBigInteger('user_id')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('kelompoks', function (Blueprint $t) {
            $t->id(); $t->string('kode', 20)->nullable()->unique(); $t->string('kode_daftar', 10)->unique();
            $t->string('status', 20)->default('menunggu')->index(); $t->text('catatan_admin')->nullable();
            $t->string('jenis'); $t->string('nama'); $t->string('sektor'); $t->string('komoditas');
            $t->string('kelas', 20)->default('Pemula'); $t->unsignedSmallInteger('tahun_berdiri')->nullable();
            $t->string('alamat'); $t->string('kecamatan'); $t->string('kelurahan');
            $t->string('ketua'); $t->string('nik_ketua', 16); $t->string('hp_ketua', 20);
            $t->string('sekretaris')->nullable(); $t->string('bendahara')->nullable(); $t->string('penyuluh')->nullable();
            $t->unsignedSmallInteger('jumlah_anggota')->default(0); $t->decimal('luas_lahan', 10, 2)->nullable();
            $t->string('no_sk')->nullable(); $t->json('anggota')->nullable();
            $t->timestamps();
        });
        Schema::create('suppliers', function (Blueprint $t) {
            $t->id(); $t->string('nama'); $t->string('alamat'); $t->string('kecamatan');
            $t->decimal('lat', 9, 6); $t->decimal('lng', 9, 6); $t->string('kontak');
            $t->string('no_sertifikat')->nullable(); $t->date('berlaku_sampai')->nullable();
            $t->json('stok')->nullable(); $t->boolean('aktif')->default(true);
            $t->timestamps();
        });
        Schema::create('antrians', function (Blueprint $t) {
            $t->id(); $t->string('kode', 10)->unique(); $t->string('lokasi'); $t->date('tanggal'); $t->unsignedSmallInteger('nomor');
            $t->string('nama'); $t->string('hp', 20); $t->string('jenis_hewan'); $t->unsignedSmallInteger('jumlah')->default(1);
            $t->string('ciri_hewan')->nullable(); $t->text('keluhan'); $t->string('layanan');
            $t->string('status', 20)->default('menunggu')->index();
            $t->timestamp('dipanggil_at')->nullable(); $t->timestamp('mulai_at')->nullable(); $t->timestamp('selesai_at')->nullable();
            $t->timestamps();
            $t->unique(['lokasi', 'tanggal', 'nomor']);
        });
        Schema::create('harga_komoditas', function (Blueprint $t) {
            $t->id(); $t->string('komoditas'); $t->string('satuan', 20)->default('kg'); $t->unsignedInteger('harga');
            $t->date('tanggal'); $t->string('pasar')->nullable(); $t->timestamps();
            $t->index(['komoditas', 'tanggal']);
        });
    }

    public function down(): void
    {
        foreach (['harga_komoditas','antrians','suppliers','kelompoks','bank_dokumen_versis','bank_dokumens'] as $x) Schema::dropIfExists($x);
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('unit_id'));
    }
};
