@extends('layouts.app')
@section('title', 'Semua Layanan - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Semua Layanan'])
@php
$tone = [
  'emerald' => 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-emerald-500/30',
  'sky'     => 'bg-gradient-to-br from-[#6B97FF] to-[#1F4FBF] text-white shadow-blue-500/30',
  'amber'   => 'bg-gradient-to-br from-amber-300 to-amber-500 text-amber-950 shadow-amber-500/30',
  'rose'    => 'bg-gradient-to-br from-rose-400 to-rose-600 text-white shadow-rose-500/30',
];
$tone['red'] = $tone['emerald']; $tone['blue'] = $tone['sky'];
$href = fn ($u) => str_starts_with((string) $u, '/') ? url($u) : ($u ?: '#');
@endphp
<main class="wrap py-10">
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($layanan as $s)
      <a href="{{ $href($s->url) }}" class="group flex items-start gap-4 rounded-2xl border border-padi-100 bg-white p-5 shadow-[0_10px_30px_rgba(31,79,191,0.10)] transition-all duration-300 hover:-translate-y-1.5 hover:border-padi-300 hover:shadow-[0_22px_40px_rgba(31,79,191,0.20)]">
        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl text-[26px] shadow-lg transition duration-300 group-hover:-rotate-6 group-hover:scale-110 {{ $tone[$s->warna] ?? $tone['sky'] }}">@include('partials.icon', ['name' => $s->icon ?: 'sprout'])</span>
        <span><b class="block font-display text-[17px] leading-snug">{{ $s->nama }}</b>@if($s->deskripsi)<span class="mt-1 block text-sm leading-relaxed text-slate-500">{{ $s->deskripsi }}</span>@endif</span>
      </a>
    @endforeach
  </div>
</main>
@endsection
