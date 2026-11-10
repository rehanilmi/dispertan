@extends('admin.layout')
@section('title', $dok->exists ? 'Kelola: '.$dok->judul : 'Tambah Dokumen')
@section('content')
@php
  $edit = $dok->exists; $u = auth()->user(); $boleh = ! $edit || $dok->bisaDiubah($u);
  $kats = \App\Models\SimbadokKategori::peta();
  $err = fn ($n) => $errors->has($n) ? '<div class="text-danger small">'.e($errors->first($n)).'</div>' : '';
@endphp
@unless($boleh)<div class="alert alert-warning">Dokumen ini milik bidang lain. Anda hanya dapat melihat dan mengunduh.</div>@endunless
<div class="row">
<div class="col-lg-7">
<div class="card"><form method="post" enctype="multipart/form-data" action="{{ $edit ? route('admin.simbadok.update', $dok) : route('admin.simbadok.store') }}">
  @csrf @if($edit) @method('PUT') @endif
  <div class="card-body">
    <div class="form-row">
      <div class="form-group col-md-6"><label>Kategori</label>
        <select name="kategori" id="kategori" class="form-control" @disabled(! $boleh)>@foreach($kats as $k => $c)<option @selected(old('kategori', $dok->kategori) === $k)>{{ $k }}</option>@endforeach</select>{!! $err('kategori') !!}</div>
      <div class="form-group col-md-6"><label>Tahun</label><input type="number" name="tahun" value="{{ old('tahun', $dok->tahun) }}" class="form-control" @readonly(! $boleh)>{!! $err('tahun') !!}</div>
    </div>
    <div id="varianBox" class="form-row">
      <div class="form-group col-md-6"><label>Jenis revisi</label>
        <select name="varian" id="varian" class="form-control" @disabled(! $boleh)>@foreach(['murni' => 'Murni (awal)', 'pergeseran' => 'Pergeseran', 'perubahan' => 'Perubahan'] as $k => $v)<option value="{{ $k }}" @selected(old('varian', $dok->varian) === $k)>{{ $v }}</option>@endforeach</select>
        <small class="text-muted">Contoh: DPA 2026 → Pergeseran 1 → Pergeseran 2 → Perubahan.</small></div>
      <div class="form-group col-md-6" id="nomorBox"><label>Pergeseran ke-</label><input type="number" min="1" max="9" name="nomor" value="{{ old('nomor', $dok->nomor ?: 1) }}" class="form-control" @readonly(! $boleh)></div>
    </div>
    <div class="form-group" id="judulBox"><label>Judul <span id="judulHint" class="text-muted small"></span></label><input name="judul" value="{{ old('judul', $dok->judul) }}" class="form-control" @readonly(! $boleh)>{!! $err('judul') !!}</div>
    <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3" class="form-control" @readonly(! $boleh)>{{ old('deskripsi', $dok->deskripsi) }}</textarea></div>
    <div class="form-row">
      <div class="form-group col-md-6"><label>Hak akses</label>
        <select name="akses" class="form-control" @disabled(! $boleh)>@foreach(['publik' => 'Publik (tampil di website)', 'internal' => 'Internal (semua staf login)', 'bidang' => 'Bidang (hanya staf bidang terkait)'] as $k => $v)<option value="{{ $k }}" @selected(old('akses', $dok->akses) === $k)>{{ $v }}</option>@endforeach</select></div>
      <div class="form-group col-md-6"><label>Bidang pemilik</label>
        @if(\App\Support\Akses::isSuper($u))<select name="unit_id" class="form-control" @disabled(! $boleh)><option value="">— tidak ada —</option>@foreach($units as $id => $n)<option value="{{ $id }}" @selected(old('unit_id', $dok->unit_id) == $id)>{{ $n }}</option>@endforeach</select>
        @else<input class="form-control" value="{{ $units[$u->unit_id] ?? '-' }}" readonly>@endif{!! $err('unit_id') !!}</div>
    </div>
    @unless($edit)
      <div class="form-group"><label>File (PDF/Word/Excel/PPT/ZIP, maks 20 MB)</label><input type="file" name="file" class="d-block">{!! $err('file') !!}</div>
      <div class="form-group"><label>Catatan versi (opsional)</label><input name="catatan" value="{{ old('catatan') }}" class="form-control"></div>
    @endunless
  </div>
  @if($boleh)<div class="card-footer"><button class="btn btn-primary">Simpan</button> <a class="btn btn-default" href="{{ route('admin.simbadok.index') }}">Kembali</a></div>@endif
</form></div>
</div>

@if($edit)
<div class="col-lg-5">
  @if($boleh)
  <div class="card card-success"><div class="card-header"><h3 class="card-title">Unggah Versi Baru</h3></div>
    <form method="post" enctype="multipart/form-data" action="{{ route('admin.simbadok.versi', $dok) }}">@csrf
      <div class="card-body">
        <p class="small text-muted">Dokumen diperbarui? Unggah file terbaru. Versi sebelumnya tetap tersimpan sebagai riwayat.</p>
        <div class="form-group"><input type="file" name="file" class="d-block" required>{!! $err('file') !!}</div>
        <div class="form-group"><label>Catatan perubahan</label><input name="catatan" class="form-control" required placeholder="mis. Revisi pagu belanja pegawai">{!! $err('catatan') !!}</div>
      </div><div class="card-footer"><button class="btn btn-success">Unggah sebagai v{{ ($dok->versis->first()->versi ?? 0) + 1 }}</button></div></form></div>
  @endif
  <div class="card"><div class="card-header"><h3 class="card-title">Riwayat Versi</h3></div>
    <table class="table table-sm mb-0">@foreach($dok->versis as $v)<tr><td><b>v{{ $v->versi }}</b></td><td>{{ $v->created_at->format('d-m-Y H:i') }}<br><small class="text-muted">{{ $v->catatan }}</small></td><td class="text-right"><a href="{{ route('admin.simbadok.unduh', $v) }}"><i class="fas fa-download"></i> {{ $v->ukuran_mb }}</a></td></tr>@endforeach</table></div>
  @if($boleh && \App\Support\Akses::bisaHapus($u))<form method="post" action="{{ route('admin.simbadok.destroy', $dok) }}" onsubmit="return confirm('Hapus dokumen beserta SEMUA versinya?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus dokumen</button></form>@endif
</div>
@endif
</div>
<script>
(function(){
  var cfg=@json(collect($kats)->map(fn ($c) => $c['varian'] ?? [])->all());
  var kat=document.getElementById('kategori'),va=document.getElementById('varian');
  function sync(){
    var izin=cfg[kat.value]||[],has=izin.length>0;
    document.getElementById('varianBox').style.display=has?'':'none';
    document.getElementById('judulHint').textContent=has?'(dibuat otomatis, mis. "'+kat.value+' Pergeseran 1 2026")':'';
    document.getElementById('judulBox').style.display=has?'none':'';
    Array.prototype.forEach.call(va.options,function(o){o.hidden=izin.indexOf(o.value)<0});
    if(has&&izin.indexOf(va.value)<0)va.value=izin[0];
    document.getElementById('nomorBox').style.display=(has&&va.value==='pergeseran')?'':'none';
  }
  kat.onchange=sync;va.onchange=sync;sync();
})();
</script>
@endsection
