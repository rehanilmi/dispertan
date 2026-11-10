@extends('layouts.app')
@section('title', 'Galeri - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Galeri Foto'])
<main class="wrap py-10">
  <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
    @forelse ($fotos as $f)
      <a href="{{ asset('storage/'.$f->gambar) }}" target="_blank" class="group relative overflow-hidden rounded-2xl bg-slate-200 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
        <img src="{{ asset('storage/'.$f->gambar) }}" alt="{{ $f->judul }}" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 p-3 text-xs font-semibold text-white">{{ $f->judul }}</span>
      </a>
    @empty<p class="col-span-full text-center text-slate-400">Belum ada foto.</p>@endforelse
  </div>
  <div class="mt-6">{{ $fotos->links() }}</div>
</main>
@endsection
