@extends('layouts.app')
@section('title', 'Tiket Antrian '.$a->no)
@section('content')
@include('partials.phead', ['t' => 'Tiket Antrian Puskeswan'])
@php $aktif = in_array($a->status, \App\Models\Antrian::AKTIF); @endphp
<main class="wrap py-10">
  <div class="mx-auto max-w-md rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-[0_20px_35px_rgba(2,132,199,0.12)]">
    <p class="text-sm text-slate-500">{{ $a->lokasi }}</p>
    <p class="mt-1 text-xs text-slate-400">{{ $a->tanggal->locale('id')->translatedFormat('l, d F Y') }}</p>
    <div class="my-4 text-7xl font-extrabold text-emerald-600">{{ $a->no }}</div>
    <span class="rounded-full bg-sky-50 px-4 py-1 text-xs font-bold uppercase text-sky-700">{{ str_replace('_', ' ', $a->status) }}</span>
    @if($aktif)
      <dl class="mt-6 grid grid-cols-3 gap-2 text-center">
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-[10px] text-slate-500">Sedang dilayani</dt><dd class="font-bold">{{ $info['sekarang'] ?? '-' }}</dd></div>
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-[10px] text-slate-500">Di depan Anda</dt><dd class="font-bold">{{ $info['depan'] }}</dd></div>
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-[10px] text-slate-500">Estimasi</dt><dd class="font-bold">±{{ $info['estimasi'] }} mnt</dd></div>
      </dl>
      <p class="mt-4 text-xs text-slate-400">Halaman diperbarui otomatis tiap 20 detik.</p>
    @endif
    <div class="mt-6 border-t border-slate-100 pt-4 text-left text-sm"><b>{{ $a->nama }}</b> · {{ $a->jenis_hewan }} ({{ $a->jumlah }} ekor)<br><span class="text-slate-500">{{ $a->layanan }}</span><br>
      <span class="text-xs text-slate-400">Kode tiket: <b>{{ $a->kode }}</b> (simpan untuk mengecek ulang)</span></div>
  </div>
</main>
@if($aktif)<script>setTimeout(function(){location.reload()},20000)</script>@endif
@endsection
