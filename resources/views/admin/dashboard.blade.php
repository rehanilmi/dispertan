@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
<div class="callout callout-info mb-4"><h5 class="mb-1">Halo, {{ auth()->user()->name }}</h5><p class="mb-0 text-muted">{{ $peran }}</p></div>
@if(! count($kartu))
  <div class="alert alert-warning">Belum ada menu yang diberikan ke akun Anda. Hubungi superadmin untuk mengatur hak akses.</div>
@else
  <div class="row">
    @foreach ($kartu as $m)
      <div class="col-xl-3 col-md-4 col-6">
        <a href="{{ \App\Support\Akses::url($m) }}" class="small-box bg-white text-dark d-block" style="box-shadow:0 1px 6px rgba(0,0,0,.08)">
          <div class="inner"><h3>{{ $m['jumlah'] ?? '→' }}</h3><p class="mb-0">{{ $m['label'] }}</p></div>
          <div class="icon"><i class="fas {{ $m['icon'] }}" style="color:#1F4FBF;opacity:.25"></i></div>
        </a>
      </div>
    @endforeach
  </div>
@endif
@endsection
