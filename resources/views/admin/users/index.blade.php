@extends('admin.layout')
@section('title', 'Pengguna & Hak Akses')
@section('content')
@php
  $peran = config('akses.peran'); $label = collect(config('akses.menu'))->map(fn ($m) => $m['label']);
  $badge = ['superadmin' => 'danger', 'admin' => 'primary', 'operator' => 'secondary'];
@endphp
<div class="card">
  <div class="card-header">
    <a class="btn btn-primary btn-sm" href="{{ route('admin.users.create') }}"><i class="fas fa-user-plus"></i> Tambah Pengguna</a>
    <form class="form-inline float-right" method="get">
      <input name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-1" placeholder="Cari nama / email">
      <select name="peran" class="form-control form-control-sm mr-1"><option value="">Semua peran</option>@foreach($peran as $k => $v)<option value="{{ $k }}" @selected(request('peran') === $k)>{{ $v }}</option>@endforeach</select>
      <button class="btn btn-sm btn-secondary">Filter</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped">
      <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Bidang/UPTD</th><th>Menu yang diizinkan</th><th style="width:100px"></th></tr></thead>
      @foreach ($rows as $r)
        @php $m = \App\Support\Akses::menus($r); @endphp
        <tr>
          <td><b>{{ $r->name }}</b> @if($r->id === auth()->id())<span class="badge badge-light">Anda</span>@endif @if($r->jabatan || $r->nip)<small class="d-block text-muted">{{ $r->jabatan }}@if($r->jabatan && $r->nip) · @endif @if($r->nip)NIP {{ $r->nip }}@endif</small>@endif</td>
          <td>{{ $r->email }}</td>
          <td><span class="badge badge-{{ $badge[$r->role] ?? 'secondary' }}">{{ __($peran[$r->role] ?? $r->role) }}</span></td>
          <td>{{ $r->unit_id ? ($units[$r->unit_id] ?? '-') : '-' }}</td>
          <td>
            @if($r->role === 'superadmin')<i>Semua menu</i>
            @elseif(! count($m))<span class="text-danger">Belum ada</span>
            @else{{ collect($m)->map(fn ($k) => __($label[$k] ?? $k))->take(4)->implode(', ') }}@if(count($m) > 4) <span class="text-muted">+{{ count($m) - 4 }}</span>@endif @endif
          </td>
          <td class="text-nowrap">
            <a class="btn btn-sm btn-warning" href="{{ route('admin.users.edit', $r) }}" aria-label="Ubah"><i class="fas fa-edit"></i></a>
            @unless($r->id === auth()->id())<form class="d-inline" method="post" action="{{ route('admin.users.destroy', $r) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" aria-label="Hapus"><i class="fas fa-trash"></i></button></form>@endunless
          </td>
        </tr>
      @endforeach
    </table>
  </div>
  <div class="card-footer">{{ $rows->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
