@extends('layouts.app')
@section('title', 'SIMBADOK - Bank Dokumen Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'SIMBADOK - Bank Dokumen'])
@php $in = 'rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-padi focus:outline-none'; $kats = array_keys(\App\Models\SimbadokKategori::peta()); @endphp
<main class="wrap py-10">
  <form method="get" class="mb-6 grid gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-[0_10px_25px_rgba(0,0,0,0.05)] md:grid-cols-[1fr_repeat(3,170px)_auto]">
    <input name="q" value="{{ request('q') }}" placeholder="Cari dokumen, mis. DPA 2026, Renja…" aria-label="Cari dokumen" class="{{ $in }}">
    <select name="kategori" aria-label="Kategori" class="{{ $in }}"><option value="">Semua kategori</option>@foreach($kats as $k)<option @selected(request('kategori') === $k)>{{ $k }}</option>@endforeach</select>
    <select name="tahun" aria-label="Tahun" class="{{ $in }}"><option value="">Semua tahun</option>@foreach($tahuns as $t)<option @selected(request('tahun') == $t)>{{ $t }}</option>@endforeach</select>
    <select name="unit" aria-label="Bidang" class="{{ $in }}"><option value="">Semua bidang</option>@foreach($units as $u)<option value="{{ $u->id }}" @selected(request('unit') == $u->id)>{{ $u->nama }}</option>@endforeach</select>
    <button class="rounded-lg bg-padi px-5 py-2 text-sm font-bold text-white hover:bg-padi-700">Cari</button>
  </form>
  <p class="mb-5 text-sm text-slate-500">{{ $total }} dokumen publik ditemukan. Dokumen internal tidak ditampilkan.</p>
  @foreach ($kats as $k)
    @if ($groups->has($k))
      <section class="mb-8">
        <h2 class="mb-3 border-l-4 border-padi pl-3 text-xl font-semibold">{{ $k }}</h2>
        @foreach ($groups[$k] as $tahun => $items)
          <div class="mb-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
            <h3 class="mb-2 text-sm font-bold text-padi">Tahun {{ $tahun }}</h3>
            @foreach ($items as $d)
              <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 py-3 first:border-0">
                <div>
                  <a href="{{ route('simbadok.show', $d) }}" class="font-semibold hover:text-padi">{{ $d->judul }}</a>
                  <div class="mt-1 flex flex-wrap gap-2 text-[11px] text-slate-500">
                    @if($d->varian !== 'murni' || ! empty(\App\Models\SimbadokKategori::peta()[$d->kategori]['varian']))<span class="rounded-full bg-padi-50 px-2 py-0.5 font-semibold text-padi">{{ $d->varian_label }}</span>@endif
                    @if($d->latestVersi)<span class="rounded-full bg-padi-50 px-2 py-0.5 font-semibold text-padi">Versi {{ $d->latestVersi->versi }}</span><span>Diperbarui {{ $d->updated_at->locale(app()->getLocale())->translatedFormat('d F Y') }}</span><span>{{ $d->latestVersi->ukuran_mb }}</span>@endif
                    @if($d->unit)<span>{{ $d->unit->nama }}</span>@endif
                  </div>
                </div>
                @if($d->latestVersi)<a href="{{ route('simbadok.unduh', $d->latestVersi) }}" class="rounded-lg bg-padi px-4 py-2 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:scale-105 transition"><i class="bi bi-download"></i> Unduh</a>@endif
              </div>
            @endforeach
          </div>
        @endforeach
      </section>
    @endif
  @endforeach
  @if ($total === 0)<p class="py-16 text-center text-slate-400">Tidak ada dokumen yang cocok.</p>@endif
</main>
@endsection
