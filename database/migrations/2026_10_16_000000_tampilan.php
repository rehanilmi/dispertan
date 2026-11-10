<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('layanans', function (Blueprint $t) {
            $t->string('icon', 40)->default('bi-flower1');
            $t->string('deskripsi')->nullable();
        });
        Schema::table('units', fn (Blueprint $t) => $t->boolean('beranda')->default(false));
        Schema::create('banners', function (Blueprint $t) {
            $t->id(); $t->string('judul'); $t->text('subjudul')->nullable(); $t->string('gambar')->nullable();
            $t->string('tombol1_label', 40)->nullable(); $t->string('tombol1_url')->nullable();
            $t->string('tombol2_label', 40)->nullable(); $t->string('tombol2_url')->nullable();
            $t->unsignedSmallInteger('urutan')->default(0); $t->boolean('aktif')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::table('units', fn (Blueprint $t) => $t->dropColumn('beranda'));
        Schema::table('layanans', fn (Blueprint $t) => $t->dropColumn(['icon', 'deskripsi']));
    }
};
