@extends('layouts.app')
@section('title', 'Dinas Pertanian Kota Padang')
@section('desc', 'Portal resmi Dinas Pertanian Kota Padang: layanan publik, berita, data kelompok tani, benih padi, dan SIPANDAI Puskeswan.')
@section('content')
@php
$card = 'rounded-[20px] border border-padi-100 bg-white shadow-[0_10px_30px_rgba(31,79,191,0.10)] transition-all duration-300 hover:-translate-y-1.5 hover:border-padi-300 hover:shadow-[0_22px_40px_rgba(31,79,191,0.20)]';
$tile = [
  'emerald' => 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-emerald-500/30',
  'sky'     => 'bg-gradient-to-br from-[#6B97FF] to-[#1F4FBF] text-white shadow-blue-500/30',
  'amber'   => 'bg-gradient-to-br from-amber-300 to-amber-500 text-amber-950 shadow-amber-500/30',
  'rose'    => 'bg-gradient-to-br from-rose-400 to-rose-600 text-white shadow-rose-500/30',
];
$tile['red'] = $tile['emerald']; $tile['blue'] = $tile['sky'];
$h2 = 'font-display text-2xl font-bold leading-tight text-foreground md:text-3xl';
$more = 'inline-flex shrink-0 items-center gap-1.5 rounded-full bg-padi px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-500/25 transition hover:bg-padi-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-padi/50 focus-visible:ring-offset-2 active:scale-[.97]';
$href = fn ($u) => str_starts_with((string) $u, '/') ? url($u) : ($u ?: '#');
$slides = $banners->isNotEmpty() ? $banners : collect([(object) ['judul' => 'Satujuan untuk Kejayaan Pertanian Kota Padang',
    'subjudul' => 'Pertanian kota yang tangguh, modern, dan berkelanjutan demi ketahanan pangan dan kesejahteraan petani.', 'gambar' => null,
    'tombol1_label' => 'Profil dinas', 'tombol1_url' => '/halaman/visi-misi', 'tombol2_label' => 'Lihat layanan', 'tombol2_url' => '/layanan']]);
$n = count($slides);
@endphp

{{-- ================= BANNER BERGERAK ================= --}}
<section id="hero" class="relative h-[540px] overflow-hidden bg-padi-900 text-white md:h-[640px]" aria-roledescription="carousel" aria-label="Banner utama">
  @foreach ($slides as $i => $b)
    @php $tag = $i ? 'h2' : 'h1'; @endphp
    <article class="hs absolute inset-0" @if($i === 0) data-active @else aria-hidden="true" @endif role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} dari {{ $n }}">
      <img class="kb absolute inset-0 h-full w-full object-cover" src="{{ \App\Support\Img::banner($b->gambar) }}" alt="" @if($i) loading="lazy" @endif>
      <div class="absolute inset-0 bg-gradient-to-r from-[#0B2A6B]/90 via-[#1F4FBF]/55 to-[#A9C0FF]/0"></div>
      <div class="absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t from-[#0B2A6B]/60 to-transparent"></div>
      <div class="relative z-10 mx-auto flex h-full max-w-[1400px] items-center px-5 md:px-8">
        <div class="max-w-2xl pb-24 md:pl-16">
          <{{ $tag }} class="rise font-display text-4xl font-bold leading-[1.04] tracking-tight drop-shadow-lg md:text-6xl" style="--d:0">{{ $b->judul }}</{{ $tag }}>
          @if($b->subjudul)<p class="rise mt-5 max-w-xl text-base leading-relaxed text-white/90 md:text-lg" style="--d:1">{{ $b->subjudul }}</p>@endif
          @if($b->tombol1_label || $b->tombol2_label)
            <div class="rise mt-8 flex flex-wrap gap-3" style="--d:2">
              @if($b->tombol1_label)<a href="{{ $href($b->tombol1_url) }}" class="rounded-full bg-white px-6 py-3 text-sm font-bold text-padi-900 shadow-lg shadow-blue-900/30 transition duration-300 hover:scale-105 hover:bg-padi-100">{{ $b->tombol1_label }}</a>@endif
              @if($b->tombol2_label)<a href="{{ $href($b->tombol2_url) }}" class="rounded-full border border-white/70 bg-white/10 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-900/20 backdrop-blur transition duration-300 hover:scale-105 hover:bg-white hover:text-padi-900">{{ $b->tombol2_label }}</a>@endif
            </div>
          @endif
        </div>
      </div>
    </article>
  @endforeach
  @if($n > 1)
    <div class="absolute inset-x-0 bottom-16 z-30"><div class="mx-auto flex max-w-[1400px] items-center gap-4 px-5 md:px-8 md:pl-24">
      <div class="flex max-w-xs flex-1 gap-2">
        @foreach ($slides as $i => $b)<button type="button" class="hd h-1.5 flex-1 overflow-hidden rounded-full bg-white/30" aria-label="Tampilkan banner {{ $i + 1 }}" @if($i === 0) data-active @endif><i class="block h-full w-0 bg-white"></i></button>@endforeach
      </div>
      <button type="button" class="hp grid h-10 w-10 place-items-center rounded-full border border-white/40 bg-white/10 backdrop-blur transition hover:bg-white hover:text-padi-900" aria-label="Banner sebelumnya"><i data-lucide="chevron-left" class="h-5 w-5"></i></button>
      <button type="button" class="hn grid h-10 w-10 place-items-center rounded-full border border-white/40 bg-white/10 backdrop-blur transition hover:bg-white hover:text-padi-900" aria-label="Banner berikutnya"><i data-lucide="chevron-right" class="h-5 w-5"></i></button>
    </div></div>
  @endif
  {{-- tepi bergerigi: motif pucuk rebung Minangkabau --}}
  <svg class="pointer-events-none absolute inset-x-0 -bottom-px z-20 h-4 w-full text-background" aria-hidden="true"><defs><pattern id="rebung" width="32" height="16" patternUnits="userSpaceOnUse"><path d="M0 16 L16 0 L32 16 Z" fill="currentColor"/></pattern></defs><rect width="100%" height="16" fill="url(#rebung)"/></svg>
</section>

{{-- ================= INFO BERJALAN ================= --}}
<div class="relative z-30 mx-auto -mt-5 flex h-14 w-[calc(100%-2rem)] max-w-[1200px] items-center gap-4 rounded-full border border-padi-100 bg-white p-1.5 shadow-[0_12px_32px_rgba(31,79,191,0.18)]">
  <span class="shrink-0 rounded-full bg-padi px-5 py-2.5 text-xs font-bold text-white">Selamat datang</span>
  <b class="hidden shrink-0 text-xs text-slate-700 sm:block">{{ now()->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</b>
  <span class="hidden h-4 w-px bg-slate-300 sm:block"></span>
  <div class="min-w-0 flex-1 overflow-hidden">
    <p class="inline-block whitespace-nowrap text-xs text-slate-500 animate-[marquee_34s_linear_infinite] hover:[animation-play-state:paused]">
      <i data-lucide="sprout" class="inline h-3.5 w-3.5 align-[-2px] text-padi"></i> Portal resmi Dinas Pertanian Kota Padang &nbsp;&bull;&nbsp; Pendaftaran pasien hewan Puskeswan kini online lewat SIPANDAI &nbsp;&bull;&nbsp; Cek stok benih padi bersertifikat di peta &nbsp;&bull;&nbsp; Dokumen DPA dan Renja tersedia di SIMBADOK
    </p>
  </div>
</div>

<main class="wrap">
  {{-- ================= LAYANAN (6) ================= --}}
  <section class="mt-14" id="layanan">
    <div class="band rounded-[28px] border border-padi-100 p-5 md:p-8">
      <div class="mb-6 flex items-center justify-between gap-4">
        <h2 class="{{ $h2 }} flex items-center gap-3"><span class="h-8 w-1.5 rounded-full bg-gradient-to-b from-padi-300 to-padi"></span>Layanan publik</h2>
        <a href="{{ route('layanan.semua') }}" class="{{ $more }}">Lihat semua layanan <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
      </div>
      <div data-stagger class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
        @foreach ($layanan as $s)
          <a href="{{ $href($s->url) }}" class="group {{ $card }} flex flex-col gap-3 p-4">
            <span class="grid h-14 w-14 place-items-center rounded-2xl shadow-lg transition duration-300 group-hover:-rotate-6 group-hover:scale-110 {{ $tile[$s->warna] ?? $tile['sky'] }}">@include('partials.icon', ['name' => $s->icon ?: 'sprout'])</span>
            <span class="font-display text-[15px] font-semibold leading-snug text-slate-900">{{ $s->nama }}</span>
            @if($s->deskripsi)<span class="line-clamp-3 text-xs leading-relaxed text-slate-500">{{ $s->deskripsi }}</span>@endif
          </a>
        @endforeach
      </div>
    </div>

    {{-- Dispertan dalam angka --}}
    <div class="relative mt-6 overflow-hidden rounded-[28px] bg-gradient-to-br from-[#0B2A6B] via-[#1F4FBF] to-[#4F7BE8] text-white shadow-[0_20px_40px_rgba(31,79,191,0.30)]">
      <div class="pointer-events-none absolute -right-16 -top-24 h-72 w-72 rounded-full bg-[#A9C0FF]/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-[#C7D8FF]/15 blur-3xl"></div>
      <div class="relative grid md:grid-cols-[1.3fr_repeat(4,1fr)]">
        <div class="p-7">
          <h3 class="font-display text-2xl font-bold leading-tight">Dispertan dalam angka</h3>
          <p class="mt-1 text-sm text-white/75">Bersumber dari data SIMLUHTAN dan layanan yang berjalan.</p>
          <a href="{{ url('/layanan/monitoring-kelompok-tani') }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-bold text-padi-900 shadow-lg transition hover:scale-105"><i data-lucide="map-pin" class="h-4 w-4"></i> Lihat peta sebaran</a>
        </div>
        @foreach ([[$stat['kelompok'], 'kelompok tani'], [$stat['anggota'], 'anggota'], [$stat['kecamatan'], 'kecamatan terpantau'], [$stat['supplier'], 'supplier benih aktif']] as [$v, $l])
          <div class="border-t border-white/15 p-7 md:border-l md:border-t-0"><div class="font-display text-4xl font-bold tabular-nums" data-count="{{ $v }}">{{ number_format($v, 0, ',', '.') }}</div><div class="mt-1 text-xs text-white/75">{{ $l }}</div></div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ================= ANTRIAN PUSKESWAN HARI INI (informasi) ================= --}}
  @php
    $stCls = ['Terdaftar' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300', 'Menunggu' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300',
              'Sedang diperiksa' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300', 'Selesai' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300'];
  @endphp
  <section class="mt-16" id="antrian">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
      <h2 class="{{ $h2 }} flex items-center gap-3"><span class="h-8 w-1.5 rounded-full bg-gradient-to-b from-padi-300 to-padi"></span>Antrian Puskeswan hari ini</h2>
      <a href="{{ route('sipandai') }}" class="{{ $more }}">Daftar lewat SIPANDAI <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
    </div>
    <div class="overflow-hidden rounded-[20px] border border-padi-100 bg-white shadow-[0_10px_30px_rgba(31,79,191,0.10)]">
      <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-padi-100 px-5 py-4">
        <span class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800"><span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span></span><span id="aq-tgl">{{ $antrian['tanggal'] }}</span></span>
        <div id="aq-chips" class="flex flex-wrap gap-2">
          @foreach (['Terdaftar', 'Menunggu', 'Sedang diperiksa', 'Selesai'] as $s)<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stCls[$s] }}" data-chip="{{ $s }}">{{ __($s) }}: {{ $antrian['ringkas'][$s] }}</span>@endforeach
        </div>
        <span class="ml-auto text-xs text-slate-400"><span>Diperbarui:</span> <span id="aq-upd">{{ $antrian['diperbarui'] }}</span></span>
      </div>
      <div class="max-h-[26rem] overflow-auto">
        <table class="w-full min-w-[720px] text-left text-sm">
          <thead class="sticky top-0 bg-slate-50 text-xs text-slate-500"><tr><th class="px-5 py-3">No. Registrasi</th><th class="px-3 py-3">Pasien</th><th class="px-3 py-3">Pemilik</th><th class="px-3 py-3">Layanan</th><th class="px-3 py-3">Waktu</th><th class="px-3 py-3">Status</th></tr></thead>
          <tbody id="aq-body">
            @forelse ($antrian['rows'] as $a)
              <tr class="border-t border-padi-100">
                <td class="px-5 py-3 font-mono text-[13px] font-semibold text-padi">{{ $a['no'] }} <span class="font-sans text-xs font-normal text-slate-400">({{ $a['urutan'] }})</span></td>
                <td class="px-3 py-3 font-semibold text-slate-800">{{ $a['hewan'] }} <span class="text-xs font-normal text-slate-400">· {{ $a['jenis'] }}</span></td>
                <td class="px-3 py-3 text-slate-600">{{ $a['pemilik'] }}</td><td class="px-3 py-3 text-slate-600">{{ $a['layanan'] }}</td><td class="px-3 py-3 text-slate-600">{{ $a['waktu'] }}</td>
                <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $stCls[$a['status']] }}">{{ $a['label'] }}</span></td>
              </tr>
            @empty
              <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada antrean hari ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <p class="border-t border-padi-100 px-5 py-3 text-xs text-slate-400">Informasi antrean saja. Nama pemilik disamarkan untuk menjaga privasi.</p>
    </div>
  </section>

  {{-- ================= BERITA ================= --}}
  <section class="mt-16">
    <div class="mb-6 flex items-center justify-between gap-4">
      <h2 class="{{ $h2 }} flex items-center gap-3"><span class="h-8 w-1.5 rounded-full bg-gradient-to-b from-padi-300 to-padi"></span>Berita terkini</h2>
      <a href="{{ route('berita.index') }}" class="{{ $more }}">Lihat semua berita <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
    </div>
    <div data-stagger class="columns-1 gap-5 sm:columns-2 lg:columns-4">
      @foreach ($berita as $i => $b)
        <a href="{{ route('berita.show', $b->slug) }}" class="group {{ $card }} mb-5 block break-inside-avoid overflow-hidden">
          <div class="relative overflow-hidden bg-padi-100">
            <img src="{{ \App\Support\Img::url($b->gambar, $i, 800, [450, 600, 520, 680][$i % 4]) }}" alt="{{ $b->judul }}" loading="lazy" class="block h-auto w-full transition duration-500 group-hover:scale-105">
            <span class="absolute left-3 top-3 rounded-full bg-padi px-3 py-1 text-[11px] font-bold text-white shadow-md">{{ $b->kategori }}</span>
          </div>
          <div class="p-4">
            <time class="inline-flex items-center gap-1 text-[11px] text-slate-400"><i data-lucide="calendar" class="h-3 w-3 text-padi"></i> {{ $b->tanggal->locale(app()->getLocale())->translatedFormat('d F Y') }}</time>
            <h3 class="mt-2 font-display text-[17px] font-semibold leading-snug text-slate-900 group-hover:text-padi">{{ $b->judul }}</h3>
            <p class="mt-2 line-clamp-3 text-[12.5px] leading-relaxed text-slate-500">{{ $b->ringkasan }}</p>
          </div>
        </a>
      @endforeach
    </div>
  </section>

  {{-- ================= BIDANG & UPTD (6) ================= --}}
  <section class="mt-14">
    <div class="mb-6 flex items-center justify-between gap-4">
      <h2 class="{{ $h2 }} flex items-center gap-3"><span class="h-8 w-1.5 rounded-full bg-gradient-to-b from-padi-300 to-padi"></span>Bidang dan UPTD</h2>
      <a href="{{ route('unit.semua') }}" class="{{ $more }}">Lihat semua bidang dan UPTD <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
    </div>
    <div data-stagger class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($units as $u)
        @php $bd = $u->tipe === 'bidang'; @endphp
        <a href="{{ $u->url }}" class="group {{ $card }} flex items-center gap-4 p-4">
          <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl shadow-lg {{ $bd ? $tile['sky'] : $tile['emerald'] }}"><i data-lucide="{{ $bd ? 'network' : 'building-2' }}" class="h-6 w-6"></i></span>
          <span class="min-w-0"><span class="block font-display text-[16px] font-semibold leading-snug text-slate-900">{{ $u->nama }}</span><span class="text-xs text-slate-500">{{ $bd ? 'Bidang' : 'Unit Pelaksana Teknis Dinas' }}</span></span>
          <i data-lucide="chevron-right" class="ml-auto h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-padi"></i>
        </a>
      @endforeach
    </div>
  </section>

  {{-- ================= REELS ================= --}}
  <section class="mt-16">
    <div class="mb-6 flex items-center justify-between gap-4">
      <h2 class="{{ $h2 }} flex items-center gap-3"><span class="h-8 w-1.5 rounded-full bg-gradient-to-b from-padi-300 to-padi"></span>Reels Instagram</h2>
      <a href="{{ \App\Models\Setting::get('instagram', 'https://www.instagram.com/') }}" target="_blank" rel="noopener" class="{{ $more }}"><i data-lucide="instagram" class="h-4 w-4"></i> Buka Instagram</a>
    </div>
    <div data-stagger class="columns-2 gap-5 lg:columns-4">
      @foreach ($reels as $i => $r)
        <a href="{{ $r->url }}" target="_blank" rel="noopener" class="group {{ $card }} relative mb-5 block break-inside-avoid overflow-hidden bg-padi-100">
          <img src="{{ \App\Support\Img::url($r->thumbnail, $i, 600, [1067, 900, 1000, 800][$i % 4]) }}" alt="{{ $r->judul }}" loading="lazy" class="block h-auto w-full transition duration-500 group-hover:scale-105">
          <div class="absolute inset-0 bg-gradient-to-t from-[#0B2A6B]/80 via-transparent to-[#0B2A6B]/10"></div>
          <span class="absolute left-1/2 top-1/2 grid h-14 w-14 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-padi shadow-lg transition group-hover:scale-110"><i data-lucide="play" class="h-6 w-6 fill-current"></i></span>
          <b class="absolute inset-x-0 bottom-0 p-4 text-sm font-semibold leading-snug text-white">{{ $r->judul }}</b>
        </a>
      @endforeach
    </div>
  </section>

  {{-- ================= PADANG MOBILE ================= --}}
  <section class="band mt-16 overflow-hidden rounded-[28px] border border-padi-100">
    <div class="flex flex-wrap items-center gap-8 p-8 md:px-12">
      <div class="grid h-24 w-24 shrink-0 place-items-center rounded-3xl bg-white shadow-xl shadow-blue-500/20"><span class="grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-[#6B97FF] to-[#1F4FBF] text-white"><i data-lucide="layout-grid" class="h-8 w-8"></i></span></div>
      <div class="min-w-[220px]"><h3 class="font-display text-4xl font-bold leading-none text-padi-900">Padang Mobile</h3><p class="mt-2 text-slate-700">Semua layanan Kota Padang dalam satu genggaman.</p><a href="#" class="{{ $more }} mt-4">Unduh sekarang</a></div>
      <ul class="min-w-[240px] flex-1 space-y-3 text-sm">
        @foreach ([['layout-grid', 'Layanan lengkap', 'Akses berbagai layanan publik dengan mudah'], ['bell', 'Informasi terkini', 'Berita dan informasi terbaru seputar Kota Padang'], ['shield-check', 'Aman dan terpercaya', 'Data aman dengan sistem yang terpercaya']] as [$ic, $t, $d])
          <li class="flex items-center gap-3"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-padi-200 bg-white text-padi"><i data-lucide="{{ $ic }}" class="h-5 w-5"></i></span><span><b class="block text-slate-900">{{ $t }}</b><span class="text-xs text-slate-500">{{ $d }}</span></span></li>
        @endforeach
      </ul>
    </div>
  </section>

  {{-- ================= ALAMAT ================= --}}
  <section class="pt-16 text-center">
    <h2 class="font-display text-3xl font-bold text-foreground">Kantor {{ \App\Models\Setting::get('nama_dinas', 'Dinas Pertanian Kota Padang') }}</h2>
    <p class="mt-1 font-semibold text-slate-500">{{ \App\Models\Setting::get('jam') }}</p>
    <div class="mx-auto mt-8 flex max-w-5xl flex-wrap items-stretch gap-6 text-left">
      <div class="min-h-[220px] flex-1 basis-[420px] overflow-hidden rounded-[24px] border border-padi-100 bg-padi-100 shadow-[0_10px_30px_rgba(31,79,191,0.10)]">
        @if(\App\Models\Setting::get('maps_embed'))<iframe class="h-full min-h-[240px] w-full border-0" src="{{ \App\Models\Setting::get('maps_embed') }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" title="Lokasi kantor"></iframe>
        @else<div class="grid h-full min-h-[220px] place-items-center text-sm text-slate-500">Peta lokasi diatur di Admin &rarr; Pengaturan Situs</div>@endif
      </div>
      <div class="flex-1 basis-[260px] space-y-4 self-center text-sm">
        <p class="flex gap-3"><i data-lucide="map-pin" class="h-5 w-5 shrink-0 text-padi"></i><b>{{ \App\Models\Setting::get('alamat') }}</b></p>
        <p class="flex gap-3"><i data-lucide="phone" class="h-5 w-5 shrink-0 text-padi"></i><b>{{ \App\Models\Setting::get('telepon') }}</b></p>
      </div>
    </div>
  </section>
</main>

<script>
(function () {
  var h = document.getElementById('hero'); if (!h) return;
  var s = [].slice.call(h.querySelectorAll('.hs')), d = [].slice.call(h.querySelectorAll('.hd')), n = s.length, cur = 0;
  function go(i) {
    cur = (i + n) % n;
    s.forEach(function (x, k) { if (k === cur) { x.setAttribute('data-active', ''); x.removeAttribute('aria-hidden'); } else { x.removeAttribute('data-active'); x.setAttribute('aria-hidden', 'true'); } });
    d.forEach(function (x, k) { k === cur ? x.setAttribute('data-active', '') : x.removeAttribute('data-active'); });
  }
  h.addEventListener('animationend', function (e) { if (e.animationName === 'prog' && n > 1) go(cur + 1); });
  d.forEach(function (x, k) { x.onclick = function () { go(k); }; });
  var p = h.querySelector('.hp'), q = h.querySelector('.hn');
  if (p) p.onclick = function () { go(cur - 1); }; if (q) q.onclick = function () { go(cur + 1); };
  var x0 = null;
  h.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, {passive: true});
  h.addEventListener('touchend', function (e) { if (x0 === null) return; var dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 50) go(cur + (dx < 0 ? 1 : -1)); x0 = null; });
})();
</script>
<script>
(function () {
  var URL_A = @json(route('sipandai.antrian')), CLS = @json($stCls ?? []);
  var body = document.getElementById('aq-body'); if (!body) return;
  function el(t, c, x) { var e = document.createElement(t); if (c) e.className = c; if (x != null) e.textContent = x; return e; }
  function render(d) {
    document.getElementById('aq-tgl').textContent = d.tanggal; document.getElementById('aq-upd').textContent = d.diperbarui;
    document.querySelectorAll('[data-chip]').forEach(function (c) { var s = c.getAttribute('data-chip'); c.textContent = (d.labels ? d.labels[s] : c.textContent.split(':')[0]) + ': ' + d.ringkas[s]; });
    body.innerHTML = '';
    if (!d.rows.length) { var tr = el('tr', 'border-t border-padi-100'), td = el('td', 'px-5 py-10 text-center text-slate-400', T('Belum ada antrean hari ini.')); td.colSpan = 6; tr.appendChild(td); body.appendChild(tr); return; }
    d.rows.forEach(function (a) {
      var tr = el('tr', 'border-t border-padi-100'), c = el('td', 'px-5 py-3 font-mono text-[13px] font-semibold text-padi', a.no); c.appendChild(el('span', 'font-sans text-xs font-normal text-slate-400', ' (' + a.urutan + ')')); tr.appendChild(c);
      var p = el('td', 'px-3 py-3 font-semibold text-slate-800', a.hewan); p.appendChild(el('span', 'text-xs font-normal text-slate-400', ' · ' + a.jenis)); tr.appendChild(p);
      [a.pemilik, a.layanan, a.waktu].forEach(function (t) { tr.appendChild(el('td', 'px-3 py-3 text-slate-600', t)); });
      var s = el('td', 'px-3 py-3'); s.appendChild(el('span', 'rounded-full px-2.5 py-1 text-xs font-semibold ' + (CLS[a.status] || ''), a.label)); tr.appendChild(s); body.appendChild(tr);
    });
  }
  setInterval(function () {
    if (document.hidden) return;
    fetch(URL_A, {headers: {'Accept': 'application/json'}}).then(function (r) { return r.ok ? r.json() : null; }).then(function (d) { if (d && d.success) render(d); }).catch(function () {});
  }, 30000);
})();
</script>

@endsection
