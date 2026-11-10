<?php
use App\Http\Controllers\Admin\{AuthController, CrudController, SipandaiAdminController, SettingController, SimbadokController, KelompokController, BantuanController, UserController, ProfilController, SipandaiKlinikController};
use App\Http\Controllers\{LandingController, LayananController, SipandaiController, SiteController};
use App\Http\Middleware\{CekMenu, LocalizeResponse, SecurityHeaders, SetLocale};
use Illuminate\Support\Facades\Route;

Route::middleware([SecurityHeaders::class, SetLocale::class, LocalizeResponse::class])->group(function () {
    Route::get('/bahasa/{locale}', function (string $locale) {
        abort_unless(in_array($locale, ['id', 'en'], true), 404);
        session(['locale' => $locale]);
        return redirect()->back();
    })->name('bahasa');

    Route::get('/', [LandingController::class, 'index'])->name('home');
    Route::get('/bidang-uptd', [LandingController::class, 'unitSemua'])->name('unit.semua');
    Route::get('/{tipe}/{slug}', [LandingController::class, 'unit'])->whereIn('tipe', ['bidang', 'uptd'])->name('unit');

    Route::get('/berita', [SiteController::class, 'beritaIndex'])->name('berita.index');
    Route::get('/berita/{slug}', [SiteController::class, 'beritaShow'])->name('berita.show');
    Route::get('/halaman/{slug}', [SiteController::class, 'page'])->name('page');
    Route::get('/dokumen', [SiteController::class, 'dokumen'])->name('dokumen');
    Route::get('/galeri', [SiteController::class, 'galeri'])->name('galeri');
    Route::get('/kontak', [SiteController::class, 'form'])->defaults('jenis', 'kontak');
    Route::get('/pengaduan', [SiteController::class, 'form'])->defaults('jenis', 'pengaduan');
    Route::get('/ppid/permohonan', [SiteController::class, 'form'])->defaults('jenis', 'ppid');
    Route::post('/kirim/{jenis}', [SiteController::class, 'kirim'])->middleware('throttle:5,1')->name('kirim');
    Route::get('/sitemap.xml', [SiteController::class, 'sitemap']);

    // ---- Layanan publik ----
    Route::prefix('layanan')->group(function () {
        Route::get('/', [LayananController::class, 'semua'])->name('layanan.semua');
        Route::get('monitoring-kelompok-tani', [LayananController::class, 'monitoringKelompok'])->name('monitoring.kelompok');
        Route::get('simbadok', [LayananController::class, 'simbadok'])->name('simbadok');
        Route::get('simbadok/unduh/{versi}', [LayananController::class, 'simbadokUnduh'])->name('simbadok.unduh');
        Route::get('simbadok/{dok}', [LayananController::class, 'simbadokShow'])->name('simbadok.show');

        Route::get('kelompok-tani', [LayananController::class, 'kelompok'])->name('kelompok');

        Route::get('benih-padi', [LayananController::class, 'benih'])->name('benih');
        Route::get('harga', [LayananController::class, 'harga'])->name('harga');
        Route::get('sipandai', [SipandaiController::class, 'form'])->name('sipandai');
        Route::post('sipandai', [SipandaiController::class, 'store'])->middleware('throttle:8,1')->name('sipandai.store');
        Route::get('sipandai/antrian', [SipandaiController::class, 'antrian'])->middleware('throttle:60,1')->name('sipandai.antrian');
        Route::get('lapor-hama', [SiteController::class, 'form'])->defaults('jenis', 'hama');

    });

    Route::prefix('admin')->group(function () {
        Route::get('login', [AuthController::class, 'form'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('auth')->name('admin.')->group(function () {
            Route::get('/', [AuthController::class, 'dashboard'])->name('dashboard');
            Route::get('profil', [ProfilController::class, 'edit'])->name('profil');
            Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');
            Route::put('profil/password', [ProfilController::class, 'password'])->name('profil.password');

            // ---- khusus superadmin ----
            Route::middleware(CekMenu::class.':setting')->group(function () {
                Route::get('pengaturan', [SettingController::class, 'edit'])->name('setting');
                Route::post('pengaturan', [SettingController::class, 'update'])->name('setting.update');
            });
            Route::middleware(CekMenu::class.':user')->prefix('pengguna')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('{user}', [UserController::class, 'update'])->name('update');
                Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
            });

            // ---- menu yang hak aksesnya diatur per pengguna ----
            Route::middleware(CekMenu::class.':simbadok')->group(function () {
                Route::get('simbadok', [SimbadokController::class, 'index'])->name('simbadok.index');
                Route::get('simbadok/create', [SimbadokController::class, 'create'])->name('simbadok.create');
                Route::post('simbadok', [SimbadokController::class, 'store'])->name('simbadok.store');
                Route::get('simbadok/unduh/{versi}', [SimbadokController::class, 'unduh'])->name('simbadok.unduh');
                Route::get('simbadok/{dok}/edit', [SimbadokController::class, 'edit'])->name('simbadok.edit');
                Route::put('simbadok/{dok}', [SimbadokController::class, 'update'])->name('simbadok.update');
                Route::post('simbadok/{dok}/versi', [SimbadokController::class, 'versi'])->name('simbadok.versi');
                Route::delete('simbadok/{dok}', [SimbadokController::class, 'destroy'])->name('simbadok.destroy');
            });

            Route::middleware(CekMenu::class.':kelompok')->prefix('kelompok-tani')->name('kelompok.')->group(function () {
                Route::get('/', [KelompokController::class, 'index'])->name('index');
                Route::get('create', [KelompokController::class, 'create'])->name('create');
                Route::post('/', [KelompokController::class, 'store'])->name('store');
                Route::post('impor/bantuan', [KelompokController::class, 'importBantuan'])->name('impor.bantuan');
                Route::post('impor/poktan', [KelompokController::class, 'importPoktan'])->name('impor.poktan');
                Route::post('impor/anggota', [KelompokController::class, 'importAnggota'])->name('impor.anggota');
                Route::get('template/{jenis}', [KelompokController::class, 'template'])->name('template');
                Route::get('{kelompok}/edit', [KelompokController::class, 'edit'])->name('edit');
                Route::put('{kelompok}/bantuan', [KelompokController::class, 'updateBantuan'])->name('bantuan');
                Route::put('{kelompok}', [KelompokController::class, 'update'])->name('update');
                Route::get('{kelompok}', [KelompokController::class, 'show'])->name('show');
                Route::delete('{kelompok}/anggota', [KelompokController::class, 'hapusAnggota'])->name('anggota.hapus');
                Route::delete('{kelompok}', [KelompokController::class, 'destroy'])->name('destroy');
            });

            Route::middleware(CekMenu::class.':sipandai_pendaftaran')->prefix('sipandai/pendaftaran')->name('sipandai.pendaftaran.')->group(function () {
                Route::get('/', [SipandaiKlinikController::class, 'pendaftaran'])->name('index');
                Route::get('tambah', [SipandaiKlinikController::class, 'tambah'])->name('tambah');
                Route::post('/', [SipandaiKlinikController::class, 'simpan'])->name('simpan');
                Route::post('impor', [SipandaiKlinikController::class, 'impor'])->name('impor');
                Route::get('template', [SipandaiKlinikController::class, 'template'])->name('template');
                Route::delete('{id}', [SipandaiKlinikController::class, 'hapus'])->name('hapus');
            });
            Route::middleware(CekMenu::class.':sipandai_pasien')->prefix('sipandai/pasien')->name('sipandai.pasien.')->group(function () {
                Route::get('/', [SipandaiKlinikController::class, 'pasien'])->name('index');
                Route::get('{id}/riwayat', [SipandaiKlinikController::class, 'riwayat'])->name('riwayat');
            });
            Route::middleware(CekMenu::class.':sipandai_antrian')->prefix('sipandai/antrian')->name('sipandai.antrian.')->group(function () {
                Route::get('/', [SipandaiKlinikController::class, 'antrian'])->name('index');
                Route::get('data', [SipandaiKlinikController::class, 'antrianData'])->name('data');
                Route::post('berikut', [SipandaiKlinikController::class, 'berikut'])->name('berikut');
                Route::post('{uid}/status', [SipandaiKlinikController::class, 'ubahStatus'])->name('status');
            });

            Route::middleware(CekMenu::class.':sipandai_rekap')->group(function () {
                Route::get('sipandai', [SipandaiAdminController::class, 'dashboard'])->name('sipandai');
                Route::get('sipandai/data', [SipandaiAdminController::class, 'data'])->name('sipandai.data');
                Route::get('sipandai/bulanan', [SipandaiAdminController::class, 'bulanan'])->name('sipandai.bulanan');
                Route::get('sipandai/bulanan/export', [SipandaiAdminController::class, 'bulananExport'])->name('sipandai.bulanan.export');
            });
            Route::middleware(CekMenu::class.':sipandai_pemeriksaan')->group(function () {
                Route::get('sipandai/pemeriksaan', [SipandaiAdminController::class, 'pemeriksaan'])->name('sipandai.pemeriksaan');
                Route::get('sipandai/pemeriksaan/cari', [SipandaiAdminController::class, 'cari'])->name('sipandai.cari');
                Route::post('sipandai/pemeriksaan/{uid}', [SipandaiAdminController::class, 'simpan'])->name('sipandai.simpan');
            });
            Route::middleware(CekMenu::class.':monitoring')->group(function () {
                Route::get('monitoring-bantuan', [BantuanController::class, 'dashboard'])->name('monitoring');
                Route::get('monitoring-bantuan/export', [BantuanController::class, 'export'])->name('monitoring.export');
            });

            // ---- CRUD umum (hak akses diperiksa per sumber daya di CrudController) ----
            Route::prefix('{res}')->whereIn('res', ['berita', 'layanan', 'unit', 'reel', 'page', 'galeri', 'dokumen', 'pesan', 'menu', 'log', 'supplier', 'harga', 'banner', 'kategori'])->group(function () {
                Route::get('/', [CrudController::class, 'index'])->name('index');
                Route::get('create', [CrudController::class, 'create'])->name('create');
                Route::post('/', [CrudController::class, 'store'])->name('store');
                Route::get('{id}/edit', [CrudController::class, 'edit'])->name('edit');
                Route::put('{id}', [CrudController::class, 'update'])->name('update');
                Route::delete('{id}', [CrudController::class, 'destroy'])->name('destroy');
            });
        });
    });
});
