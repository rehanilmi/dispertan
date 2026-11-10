<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ID Poktan boleh kosong (data lapangan sering belum punya ID); kualitas data dicek di dashboard.
        Schema::table('kelompoks', fn (Blueprint $t) => $t->string('id_poktan', 20)->nullable()->change());

        Schema::create('kelompok_bantuans', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('kelompok_id')->index();
            $t->unsignedSmallInteger('tahun')->index();
            $t->string('sumber', 30);            // APBD Kota | APBD Provinsi | APBN
            $t->string('keterangan', 500);
            $t->timestamps();
            $t->unique(['kelompok_id', 'tahun', 'sumber']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_bantuans');
    }
};
