@extends('admin.layout')
@section('title', $kelompok->exists ? 'Ubah: '.$kelompok->nama : 'Tambah Kelompok Tani')
@section('content')
@php
  $kec = collect(config('layanan.kecamatan'))->map(fn ($x) => mb_strtoupper($x))->merge(\App\Models\Kelompok::whereNotNull('kecamatan')->distinct()->pluck('kecamatan'))->unique()->sort()->values();
  $f = fn ($n, $l, $t = 'text', $extra = '') => '<div class="form-group"><label>'.$l.'</label><input type="'.$t.'" name="'.$n.'" value="'.e(old($n, $kelompok->$n)).'" class="form-control'.($errors->has($n) ? ' is-invalid' : '').'" '.$extra.'>'.($errors->has($n) ? '<div class="text-danger small">'.e($errors->first($n)).'</div>' : '').'</div>';
@endphp
<div class="card"><form method="post" action="{{ $kelompok->exists ? route('admin.kelompok.update', $kelompok) : route('admin.kelompok.store') }}">
  @csrf @if($kelompok->exists) @method('PUT') @endif
  <div class="card-body">
    {!! $f('nama', 'Nama Poktan') !!}{!! $f('id_poktan', 'ID Poktan (boleh kosong jika belum ada)') !!}{!! $f('jumlah_anggota', 'Jumlah Anggota', 'number', 'min="0"') !!}
    {!! $f('kecamatan', 'Kecamatan (tulis "Bungus Teluk Kabung" atau dengan awalan "Kecamatan" - otomatis diseragamkan)', 'text', 'list="kec"') !!}
    <datalist id="kec">@foreach($kec as $k)<option value="{{ $k }}">@endforeach</datalist>
    {!! $f('desa', 'Nama Desa/Kelurahan') !!}{!! $f('ketua', 'Nama Ketua') !!}{!! $f('alamat_sekretariat', 'Alamat Sekretariat') !!}
  </div>
  <div class="card-footer"><button class="btn btn-primary">Simpan</button> <a class="btn btn-default" href="{{ $kelompok->exists ? route('admin.kelompok.show', $kelompok) : route('admin.kelompok.index') }}">Batal</a></div>
</form></div>
@endsection
