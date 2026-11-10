@extends('layouts.app')
@section('title', 'Papan Antrian Puskeswan')
@section('content')
@include('partials.phead', ['t' => 'Papan Antrian Puskeswan'])
<main class="wrap py-10">
  <p class="mb-4 text-sm text-slate-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }} · diperbarui otomatis</p>
  <div class="grid gap-5 md:grid-cols-2">
    @foreach($papan as $p)
      <div class="rounded-3xl border border-slate-100 bg-white p-6 text-center shadow-[0_20px_35px_rgba(2,132,199,0.12)]">
        <h2 class="font-semibold">{{ $p['lokasi'] }}</h2>
        <div class="text-xs text-slate-400">Sedang dilayani</div>
        <div class="my-2 text-6xl font-extrabold text-emerald-600">{{ $p['sekarang'] ?? '—' }}</div>
        <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs"><div class="rounded-xl bg-amber-50 p-2"><b class="text-lg">{{ $p['menunggu'] }}</b><br>Menunggu</div><div class="rounded-xl bg-emerald-50 p-2"><b class="text-lg">{{ $p['selesai'] }}</b><br>Selesai</div><div class="rounded-xl bg-sky-50 p-2"><b class="text-lg">{{ $p['total'] }}</b><br>Total hari ini</div></div>
      </div>
    @endforeach
  </div>
  <a href="{{ route('puskeswan') }}" class="mt-6 inline-block rounded-lg bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/20">Daftar Antrian Online</a>
</main>
<script>setTimeout(function(){location.reload()},15000)</script>
@endsection
