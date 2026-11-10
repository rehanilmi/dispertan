@extends('layouts.app')
@section('title', $b->judul.' - Dinas Pertanian Kota Padang')
@section('desc', \Illuminate\Support\Str::limit($b->ringkasan, 150))
@section('og', \App\Support\Img::url($b->gambar, 0, 1200, 630))
@section('content')
@include('partials.phead', ['t' => 'Berita'])
<main class="wrap grid gap-8 py-10 lg:grid-cols-[1fr_320px]">
  <article>
    <span class="rounded-full bg-padi px-3 py-1 text-[11px] font-bold text-white">{{ $b->kategori }}</span>
    <h2 class="mt-3 text-3xl font-extrabold leading-tight text-slate-800">{{ $b->judul }}</h2>
    <time class="mt-2 block text-sm text-slate-400">{{ $b->tanggal->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</time>
    <img src="{{ \App\Support\Img::url($b->gambar, 0, 1200, 675) }}" alt="{{ $b->judul }}" class="mt-5 aspect-video w-full rounded-2xl object-cover">
    <div class="prose prose-slate mt-6 max-w-none">{!! $b->konten ?: '<p>'.e($b->ringkasan).'</p>' !!}</div>
  </article>
  <aside>
    <h3 class="mb-3 border-l-4 border-padi pl-3 font-semibold">Berita Lainnya</h3>
    @foreach ($lain as $x)
      <a href="{{ route('berita.show', $x->slug) }}" class="mb-3 block rounded-xl border border-slate-100 bg-white p-3 text-sm font-semibold shadow-sm hover:text-padi">{{ $x->judul }}<small class="mt-1 block font-normal text-slate-400">{{ $x->tanggal->locale(app()->getLocale())->translatedFormat('d F Y') }}</small></a>
    @endforeach
  </aside>
</main>
@endsection
