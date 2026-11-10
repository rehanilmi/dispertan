@extends('layouts.app')
@section('title', $j[0].' - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => $j[0]])
@php $in = 'mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-padi focus:outline-none focus:ring-2 focus:ring-padi/25'; @endphp
<main class="wrap py-10">
  <form method="post" action="{{ route('kirim', $jenis) }}" class="mx-auto max-w-2xl space-y-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
    @csrf
    @if(session('ok'))<div class="rounded-lg bg-padi-50 p-3 text-sm font-semibold text-padi" role="status">{{ session('ok') }}</div>@endif
    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
    @foreach ([['nama','Nama lengkap','text'],['email','Email','email'],['telepon','Telepon / WhatsApp (opsional)','text'],['subjek',$j[1],'text']] as [$n,$l,$ty])
      <label class="block text-sm font-semibold">{{ $l }}<input type="{{ $ty }}" name="{{ $n }}" value="{{ old($n) }}" class="{{ $in }}">@error($n)<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror</label>
    @endforeach
    <label class="block text-sm font-semibold">Isi pesan<textarea name="isi" rows="6" class="{{ $in }}">{{ old('isi') }}</textarea>@error('isi')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror</label>
    <button class="rounded-lg bg-padi px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/25 transition hover:scale-105">Kirim</button>
  </form>
</main>
@endsection
