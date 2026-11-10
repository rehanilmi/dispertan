<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        // Layanan Puskeswan versi antrian lama dihapus, diganti SIPANDAI (port dari Google Apps Script)
        Schema::dropIfExists('antrians');

        // Struktur kolom = HEADER_PENDAFTARAN di Code.gs (1 baris per hewan, 1 nomor pendaftaran dibagi per pemilik)
        Schema::create('pendaftarans', function (Blueprint $t) {
            $t->id();
            $t->string('uid', 8)->unique();                       // ID_Pendaftaran
            $t->string('no_pendaftaran', 30)->index();            // REG-PUSKESWAN-YYYYMMDD-XXX
            $t->string('urutan_hewan', 10);                       // mis. "1/2"
            $t->date('tanggal')->index(); $t->time('waktu');
            $t->string('nama_pemilik', 100); $t->string('nik', 16)->index(); $t->text('alamat'); $t->string('no_hp', 20);
            $t->string('nama_hewan', 100); $t->string('jenis_hewan', 30)->index(); $t->string('ras', 100)->nullable();
            $t->string('jenis_kelamin', 10); $t->string('umur_tgl_lahir', 60); $t->string('ciri_khusus', 200)->nullable();
            $t->string('jenis_pelayanan', 60)->index(); $t->text('keluhan_utama'); $t->text('info_tambahan')->nullable();
            $t->date('skkh_tanggal')->nullable(); $t->string('skkh_nama_penerima')->nullable(); $t->text('skkh_alamat_penerima')->nullable();
            $t->unsignedSmallInteger('skkh_jumlah_jantan')->nullable(); $t->unsignedSmallInteger('skkh_jumlah_betina')->nullable();
            $t->string('status_pelayanan', 15)->default('Menunggu')->index();   // Menunggu | Selesai
            $t->text('diagnosa')->nullable(); $t->text('jenis_tindakan')->nullable(); $t->text('catatan_pemeriksaan')->nullable();
            $t->string('petugas_update')->nullable(); $t->timestamp('waktu_update')->nullable();
            $t->timestamps();
            $t->unique(['no_pendaftaran', 'urutan_hewan']);
        });

        // Rapikan data tampilan yang sudah ada
        if (Schema::hasTable('layanans') && Schema::hasColumn('layanans', 'icon')) {
            DB::table('layanans')->where('url', '/layanan/puskeswan/antrian')->delete();
            DB::table('layanans')->where('url', '/layanan/puskeswan')->update([
                'nama' => 'SIPANDAI', 'url' => '/layanan/sipandai', 'icon' => 'paw-print',
                'deskripsi' => 'Pendaftaran pasien hewan Puskeswan secara online.',
            ]);
        }
        if (Schema::hasTable('banners')) {
            DB::table('banners')->where('tombol1_url', '/layanan/puskeswan')->update(['tombol1_label' => 'Daftar lewat SIPANDAI', 'tombol1_url' => '/layanan/sipandai']);
            DB::table('banners')->where('tombol2_url', '/layanan/puskeswan/antrian')->update(['tombol2_label' => null, 'tombol2_url' => null]);
            DB::table('banners')->where('judul', 'Daftar antrian Puskeswan dari rumah')->update([
                'judul' => 'Daftarkan hewan Anda ke Puskeswan secara online',
                'subjudul' => 'Isi data sekali, daftarkan lebih dari satu hewan, dan simpan struk pendaftaran Anda.',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
