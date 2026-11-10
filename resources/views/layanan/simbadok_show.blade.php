@extends('layouts.app')
@section('title', $dok->judul.' - SIMBADOK')
@section('content')
@include('partials.phead', ['t' => $dok->judul])
<main class="wrap grid gap-6 py-10 lg:grid-cols-[1fr_340px]">
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
      <p class="text-sm text-slate-500">{{ $dok->kategori }} · {{ $dok->tahun }} @if($dok->unit)· {{ $dok->unit->nama }}@endif</p>
      @if($dok->deskripsi)<p class="mt-3 text-sm leading-relaxed">{{ $dok->deskripsi }}</p>@endif
      @if($dok->versis->first())<a href="{{ route('simbadok.unduh', $dok->versis->first()) }}" class="mt-4 inline-block rounded-lg bg-padi px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/25 hover:scale-105 transition"><i class="bi bi-download"></i> Unduh versi terbaru (v{{ $dok->versis->first()->versi }})</a>@endif
    </section>
    <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
      <h2 class="mb-3 font-semibold">Riwayat Versi</h2>
      <table class="w-full text-left text-sm"><thead class="text-xs text-slate-500"><tr><th class="py-2">Versi</th><th>Tanggal</th><th>Catatan perubahan</th><th></th></tr></thead>
      @foreach($dok->versis as $v)<tr class="border-t border-slate-100"><td class="py-2 font-bold">v{{ $v->versi }} @if($loop->first)<span class="ml-1 rounded bg-padi-50 px-1.5 text-[10px] text-padi">terbaru</span>@endif</td><td>{{ $v->created_at->locale(app()->getLocale())->translatedFormat('d F Y') }}</td><td>{{ $v->catatan }}</td><td class="text-right"><a class="text-padi hover:underline" href="{{ route('simbadok.unduh', $v) }}">Unduh ({{ $v->ukuran_mb }})</a></td></tr>@endforeach</table>
    </section>
  </div>
  <aside class="rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
    <h3 class="mb-3 font-semibold">Rangkaian {{ $dok->kategori }} {{ $dok->tahun }}</h3>
    @foreach($keluarga as $k)
      <a href="{{ route('simbadok.show', $k) }}" class="mb-2 flex items-center justify-between rounded-xl px-3 py-2 text-sm {{ $k->id === $dok->id ? 'bg-padi-50 font-bold text-padi' : 'hover:bg-padi-50' }}">
        <span>{{ $k->varian_label }}</span><span class="text-[11px] text-slate-400">v{{ $k->latestVersi?->versi }}</span></a>
    @endforeach
    <a href="{{ route('simbadok') }}" class="mt-3 block text-xs text-padi hover:underline">&larr; Kembali ke SIMBADOK</a>
  </aside>
</main>
@endsection
