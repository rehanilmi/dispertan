<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        // Hak akses per menu: daftar kunci menu (JSON) per pengguna. Superadmin tidak memakainya (akses penuh).
        Schema::table('users', fn (Blueprint $t) => $t->json('menus')->nullable());
        // Akun lama berperan "admin" (akses penuh) menjadi superadmin; operator lama mendapat menu bawaan yang dulu berlaku.
        DB::table('users')->where('role', 'admin')->update(['role' => 'superadmin']);
        DB::table('users')->where('role', 'operator')->update(['menus' => json_encode(['berita', 'galeri', 'dokumen', 'reel', 'pesan'])]);

        // Kategori SIMBADOK pindah dari config ke database agar superadmin bisa menambah sendiri.
        Schema::create('simbadok_kategoris', function (Blueprint $t) {
            $t->id(); $t->string('nama', 40)->unique();
            $t->string('revisi', 20)->nullable();      // null = judul manual | penuh = Murni/Pergeseran/Perubahan | murni_perubahan
            $t->unsignedSmallInteger('urutan')->default(0);
            $t->timestamps();
        });
        $i = 0;
        foreach ((array) config('layanan.simbadok.kategori') as $nama => $c) {
            $n = count($c['varian'] ?? []);
            DB::table('simbadok_kategoris')->insert(['nama' => $nama, 'revisi' => $n >= 3 ? 'penuh' : ($n === 2 ? 'murni_perubahan' : null),
                'urutan' => $i++, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('simbadok_kategoris');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('menus'));
    }
};
