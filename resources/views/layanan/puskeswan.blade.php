@extends('layouts.app')
@section('title', 'Layanan Puskeswan - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Layanan Puskeswan'])
@php $in = 'mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20'; $err = fn ($n) => $errors->has($n) ? '<span class="text-xs font-normal text-red-600">'.e($errors->first($n)).'</span>' : ''; @endphp
<main class="wrap grid gap-6 py-10 lg:grid-cols-[1fr_320px]">
  <form method="post" action="{{ route('puskeswan.daftar') }}" class="grid gap-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)] sm:grid-cols-2">
    @csrf <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
    <h2 class="font-semibold sm:col-span-2">Pendaftaran Antrian Online</h2>
    <label class="block text-sm font-semibold">Lokasi<select name="lokasi" class="{{ $in }}">@foreach($cfg['lokasi'] as $x)<option @selected(old('lokasi') === $x)>{{ $x }}</option>@endforeach</select>{!! $err('lokasi') !!}</label>
    <label class="block text-sm font-semibold">Tanggal kunjungan<select name="tanggal" class="{{ $in }}">@foreach($hari as $k => $v)<option value="{{ $k }}" @selected(old('tanggal') === $k)>{{ $v }}</option>@endforeach</select>{!! $err('tanggal') !!}</label>
    <label class="block text-sm font-semibold">Nama pemilik<input name="nama" value="{{ old('nama') }}" class="{{ $in }}">{!! $err('nama') !!}</label>
    <label class="block text-sm font-semibold">No. HP/WA<input name="hp" value="{{ old('hp') }}" class="{{ $in }}">{!! $err('hp') !!}</label>
    <label class="block text-sm font-semibold">Jenis hewan<select name="jenis_hewan" class="{{ $in }}">@foreach($cfg['hewan'] as $x)<option @selected(old('jenis_hewan') === $x)>{{ $x }}</option>@endforeach</select></label>
    <label class="block text-sm font-semibold">Jumlah ekor<input type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}" class="{{ $in }}">{!! $err('jumlah') !!}</label>
    <label class="block text-sm font-semibold">Nama/ciri hewan (opsional)<input name="ciri_hewan" value="{{ old('ciri_hewan') }}" class="{{ $in }}"></label>
    <label class="block text-sm font-semibold">Jenis layanan<select name="layanan" class="{{ $in }}">@foreach($cfg['layanan'] as $x)<option @selected(old('layanan') === $x)>{{ $x }}</option>@endforeach</select></label>
    <label class="block text-sm font-semibold sm:col-span-2">Keluhan / keperluan<textarea name="keluhan" rows="3" class="{{ $in }}">{{ old('keluhan') }}</textarea>{!! $err('keluhan') !!}</label>
    <button class="rounded-lg bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 transition hover:scale-105 sm:col-span-2">Ambil Nomor Antrian</button>
  </form>
  <aside class="space-y-4">
    <a href="{{ route('puskeswan.antrian') }}" class="block rounded-2xl bg-gradient-to-br from-blue-900 to-sky-600 p-5 text-white shadow-lg transition hover:-translate-y-1"><div class="text-xs opacity-80">Pantau langsung</div><div class="text-xl font-extrabold">Papan Antrian Hari Ini &rarr;</div></a>
    <form method="get" action="{{ route('puskeswan.cek') }}" class="rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
      <label class="text-sm font-semibold">Cek tiket Anda<input name="kode" placeholder="Kode tiket" class="{{ $in }}"></label><button class="mt-2 w-full rounded-lg bg-sky-600 py-2 text-sm font-bold text-white">Cek</button></form>
    <p class="text-xs text-slate-500">Layanan buka hari kerja. Kuota {{ $cfg['kuota'] }} antrian per hari. Datang sesuai tanggal terdaftar; nomor yang terlewat dapat dinyatakan tidak hadir.</p>
  </aside>
</main>
@endsection
