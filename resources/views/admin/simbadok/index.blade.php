@extends('admin.layout')
@section('title', 'SIMBADOK - Bank Dokumen')
@section('content')
<div class="card">
  <div class="card-header">
    <a class="btn btn-primary btn-sm" href="{{ route('admin.simbadok.create') }}"><i class="fas fa-plus"></i> Tambah Dokumen</a>
    <form class="form-inline float-right" method="get">
      <input name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-1" placeholder="Cari judul/deskripsi">
      <select name="kategori" class="form-control form-control-sm mr-1"><option value="">Semua kategori</option>@foreach(array_keys(\App\Models\SimbadokKategori::peta()) as $k)<option @selected(request('kategori') === $k)>{{ $k }}</option>@endforeach</select>
      <input name="tahun" value="{{ request('tahun') }}" class="form-control form-control-sm mr-1" style="width:80px" placeholder="Tahun">
      <button class="btn btn-sm btn-secondary">Filter</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped">
      <thead><tr><th>Dokumen</th><th>Kategori</th><th>Akses</th><th>Bidang</th><th>Versi</th><th>Diperbarui</th><th style="width:100px"></th></tr></thead>
      @forelse($rows as $d)
        <tr>
          <td><b>{{ $d->judul }}</b></td><td>{{ $d->kategori }} {{ $d->tahun }}</td>
          <td><span class="badge badge-{{ ['publik' => 'success', 'internal' => 'info', 'bidang' => 'warning'][$d->akses] }}">{{ $d->akses }}</span></td>
          <td>{{ $d->unit?->nama ?? '-' }}</td><td>v{{ $d->latestVersi?->versi }}</td><td>{{ $d->updated_at->format('d-m-Y') }}</td>
          <td class="text-nowrap">
            @if($d->latestVersi)<a class="btn btn-sm btn-default" title="Unduh" href="{{ route('admin.simbadok.unduh', $d->latestVersi) }}"><i class="fas fa-download"></i></a>@endif
            <a class="btn btn-sm btn-warning" title="{{ $d->bisaDiubah(auth()->user()) ? 'Kelola' : 'Lihat' }}" href="{{ route('admin.simbadok.edit', $d) }}"><i class="fas fa-edit"></i></a>
          </td>
        </tr>
      @empty<tr><td colspan="7" class="text-center text-muted">Belum ada dokumen yang dapat Anda akses.</td></tr>@endforelse
    </table>
  </div>
  <div class="card-footer">{{ $rows->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
