@extends('layouts.app')
@section('title', 'Berita - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Berita'])
<main class="wrap py-10">
  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($berita as $i => $b)
      <a href="{{ route('berita.show', $b->slug) }}" class="group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_10px_25px_rgba(0,0,0,0.05)] transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_20px_35px_rgba(2,132,199,0.12)]">
        <div class="relative overflow-hidden bg-slate-200">
          <img src="{{ \App\Support\Img::url($b->gambar, $i) }}" alt="{{ $b->judul }}" loading="lazy" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
          <span class="absolute left-3 top-3 rounded-full bg-padi px-3 py-1 text-[11px] font-bold text-white">{{ $b->kategori }}</span>
        </div>
        <div class="p-4">
          <time class="text-[11px] text-slate-400">{{ $b->tanggal->locale(app()->getLocale())->translatedFormat('d F Y') }}</time>
          <h3 class="mt-2 font-bold leading-snug text-slate-800 group-hover:text-padi">{{ $b->judul }}</h3>
          <p class="mt-2 line-clamp-3 text-xs text-slate-500">{{ $b->ringkasan }}</p>
        </div>
      </a>
    @endforeach
  </div>
  <div class="mt-8">{{ $berita->links() }}</div>
</main>
@endsection
