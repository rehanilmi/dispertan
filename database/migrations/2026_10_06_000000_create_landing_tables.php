<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanans', function (Blueprint $t) {
            $t->id(); $t->string('nama'); $t->string('url')->nullable()->default('#');
            $t->string('warna', 8)->default('red'); $t->unsignedSmallInteger('urutan')->default(0); $t->timestamps();
        });
        Schema::create('beritas', function (Blueprint $t) {
            $t->id(); $t->string('judul'); $t->text('ringkasan'); $t->string('gambar')->nullable();
            $t->string('url')->nullable()->default('#'); $t->date('tanggal'); $t->timestamps();
        });
        Schema::create('units', function (Blueprint $t) {
            $t->id(); $t->string('tipe', 10)->index(); $t->string('nama'); $t->string('slug')->index();
            $t->text('ringkasan')->nullable(); $t->longText('konten')->nullable(); $t->string('gambar')->nullable();
            $t->unsignedSmallInteger('urutan')->default(0); $t->timestamps();
        });
        Schema::create('reels', function (Blueprint $t) {
            $t->id(); $t->string('judul'); $t->string('url'); $t->string('thumbnail')->nullable();
            $t->unsignedSmallInteger('urutan')->default(0); $t->timestamps();
        });
    }
    public function down(): void
    {
        foreach (['reels', 'units', 'beritas', 'layanans'] as $x) Schema::dropIfExists($x);
    }
};
