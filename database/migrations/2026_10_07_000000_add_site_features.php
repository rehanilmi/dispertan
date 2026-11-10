<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $t) {
            $t->string('slug')->nullable()->index();
            $t->longText('konten')->nullable();
            $t->string('kategori', 40)->default('Berita');
        });
        foreach (DB::table('beritas')->get() as $b) {
            DB::table('beritas')->where('id', $b->id)->update(['slug' => Str::slug($b->judul).'-'.$b->id]);
        }
        Schema::table('users', fn (Blueprint $t) => $t->string('role', 20)->default('operator'));
        DB::table('users')->where('id', DB::table('users')->min('id'))->update(['role' => 'admin']);

        Schema::create('pages', function (Blueprint $t) {
            $t->id(); $t->string('judul'); $t->string('slug')->unique(); $t->longText('konten')->nullable(); $t->timestamps();
        });
        Schema::create('dokumens', function (Blueprint $t) {
            $t->id(); $t->string('judul'); $t->string('kategori', 40); $t->string('file'); $t->unsignedSmallInteger('tahun')->nullable(); $t->timestamps();
        });
        Schema::create('galeris', function (Blueprint $t) {
            $t->id(); $t->string('judul'); $t->string('gambar'); $t->timestamps();
        });
        Schema::create('pesans', function (Blueprint $t) {
            $t->id(); $t->string('jenis', 20); $t->string('nama'); $t->string('email'); $t->string('telepon', 30)->nullable();
            $t->string('subjek'); $t->text('isi'); $t->string('status', 20)->default('baru'); $t->timestamps();
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->id(); $t->string('key')->unique(); $t->text('value')->nullable();
        });
        Schema::create('menus', function (Blueprint $t) {
            $t->id(); $t->string('label'); $t->string('url')->nullable(); $t->string('kolom')->nullable();
            $t->unsignedBigInteger('parent_id')->nullable()->index(); $t->unsignedSmallInteger('urutan')->default(0); $t->timestamps();
        });
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id')->nullable(); $t->string('aksi', 30); $t->string('deskripsi');
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('visits', function (Blueprint $t) {
            $t->id(); $t->date('tanggal')->unique(); $t->unsignedInteger('hits')->default(0);
        });
    }

    public function down(): void
    {
        foreach (['visits','activity_logs','menus','settings','pesans','galeris','dokumens','pages'] as $x) Schema::dropIfExists($x);
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
        Schema::table('beritas', fn (Blueprint $t) => $t->dropColumn(['slug', 'konten', 'kategori']));
    }
};
