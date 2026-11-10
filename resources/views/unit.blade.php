@extends('layouts.app')
@section('title', $unit->nama.' - Dinas Pertanian Kota Padang')
@section('content')
<section class="uhead"><div class="wrap">
  <small><a href="{{ route('home') }}">Beranda</a> / {{ strtoupper($unit->tipe) }} / {{ $unit->nama }}</small>
  <h1>{{ $unit->nama }}</h1>
  @if($unit->ringkasan)<p>{{ $unit->ringkasan }}</p>@endif
</div></section>
<main class="wrap upage">
  <article class="ucard">
    @if($unit->gambar)<img src="{{ asset('storage/'.$unit->gambar) }}" alt="{{ $unit->nama }}">@endif
    <div class="ubody">{!! $unit->konten !!}</div>
  </article>
  <aside class="ucard side">
    <h4>Bidang &amp; UPTD</h4>
    @foreach($units as $u)
      <a href="{{ $u->url }}" @class(['act' => $u->id === $unit->id])>{{ $u->nama }} <small>{{ strtoupper($u->tipe) }}</small></a>
    @endforeach
  </aside>
</main>
@endsection
