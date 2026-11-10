@extends('admin.layout')
@section('title', $kelompok->nama)
@section('content')
<div class="row">
  <div class="col-lg-4"><div class="card"><div class="card-body">
    <dl class="mb-0 small">
      <dt>ID Poktan</dt><dd>{{ $kelompok->id_poktan }}</dd>
      <dt>Desa / Kecamatan</dt><dd>{{ $kelompok->desa }} / {{ $kelompok->kecamatan ?: '-' }}</dd>
      <dt>Kabupaten, Provinsi</dt><dd>{{ $kelompok->kabupaten ?: '-' }}, {{ $kelompok->provinsi ?: '-' }}</dd>
      <dt>Ketua</dt><dd>{{ $kelompok->ketua }}</dd>
      <dt>Alamat Sekretariat</dt><dd>{{ $kelompok->alamat_sekretariat }}</dd>
      <dt>Anggota</dt><dd>{{ $kelompok->jumlah_anggota }} (SIMLUH) · {{ $stat->n }} terdata ({{ (int) $stat->l }} L / {{ (int) $stat->p }} P)</dd>
      <dt>Total luas diusahakan / dimiliki</dt><dd>{{ number_format($stat->diusahakan, 2, ',', '.') }} ha / {{ number_format($stat->dimiliki, 2, ',', '.') }} ha</dd>
    </dl>
  </div>
  <div class="card-footer">
    <a href="{{ route('admin.kelompok.index') }}" class="btn btn-default btn-sm">Kembali</a> <a href="{{ route('admin.kelompok.edit', $kelompok) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Ubah data</a>
    <form class="d-inline" method="post" action="{{ route('admin.kelompok.anggota.hapus', $kelompok) }}" onsubmit="return confirm('Hapus SEMUA anggota kelompok ini?')">@csrf @method('DELETE')<button class="btn btn-outline-warning btn-sm">Kosongkan anggota</button></form>
    <form class="d-inline" method="post" action="{{ route('admin.kelompok.destroy', $kelompok) }}" onsubmit="return confirm('Hapus kelompok beserta anggotanya?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus kelompok</button></form>
  </div></div></div>
  <div class="col-lg-8"><div class="card">
    <div class="card-header"><form class="form-inline" method="get"><input name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2" placeholder="Cari nama / NIK"><button class="btn btn-sm btn-secondary">Cari</button></form></div>
    <div class="card-body table-responsive p-0">
      <table class="table table-sm table-striped">
        <thead><tr><th>Nama</th><th>NIK</th><th>Lahir</th><th>JK</th><th>HP</th><th>Status</th><th>Komoditas</th><th class="text-right">Luas (ha)</th><th>Kategori</th><th>Titik</th></tr></thead>
        @forelse($anggota as $a)
          <tr><td><b>{{ $a->nama }}</b><br><small class="text-muted">{{ $a->alamat }}</small></td><td>{{ $a->nik }}</td>
            <td>{{ $a->tempat_lahir }}@if($a->tgl_lahir), {{ $a->tgl_lahir->format('d-m-Y') }}@endif</td><td>{{ $a->jk }}</td><td>{{ $a->no_hp }}</td><td>{{ $a->status_keanggotaan }}</td>
            <td>@foreach([1, 2, 3] as $i)@if($a->{"komoditas_$i"}){{ $a->{"komoditas_$i"} }} ({{ rtrim(rtrim(number_format((float) $a->{"volume_$i"}, 2, ',', ''), '0'), ',') }})<br>@endif @endforeach{{ $a->komoditas_lain }}</td>
            <td class="text-right">{{ $a->luas_diusahakan !== null ? rtrim(rtrim(number_format($a->luas_diusahakan, 4, ',', ''), '0'), ',') : '' }}</td><td>{{ $a->kategori_petani }}</td>
            <td>@if($a->lat)<a target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $a->lat }},{{ $a->lng }}"><i class="fas fa-map-marker-alt"></i></a>@endif</td></tr>
        @empty<tr><td colspan="10" class="text-center text-muted">Belum ada anggota. Impor lewat Langkah 2.</td></tr>@endforelse
      </table>
    </div>
    <div class="card-footer">{{ $anggota->links('pagination::bootstrap-4') }}</div>
  </div></div>
</div>
<div class="card card-warning">
  <div class="card-header"><h3 class="card-title">Riwayat Bantuan — update data</h3></div>
  <form method="post" action="{{ route('admin.kelompok.bantuan', $kelompok) }}">@csrf @method('PUT')
    <div class="card-body table-responsive p-0">
      <table class="table table-sm mb-0">
        <thead><tr><th style="width:110px">Tahun</th>@foreach(config('layanan.sumber_dana') as $s)<th>{{ $s }}</th>@endforeach</tr></thead>
        @foreach($tahunList as $y)
          <tr><td><b>{{ $y }}</b></td>@foreach(config('layanan.sumber_dana') as $s)<td><input class="form-control form-control-sm" name="bantuan[{{ $y }}][{{ $s }}]" value="{{ $bm[$y][$s] ?? '' }}" placeholder="keterangan bantuan"></td>@endforeach</tr>
        @endforeach
        <tr class="table-light"><td><input type="number" name="tahun_baru" class="form-control form-control-sm" placeholder="Tahun lain"></td>@foreach(config('layanan.sumber_dana') as $s)<td><input class="form-control form-control-sm" name="baru[{{ $s }}]" placeholder="{{ $s }}"></td>@endforeach</tr>
      </table>
    </div>
    <div class="card-footer"><button class="btn btn-warning">Simpan riwayat bantuan</button> <small class="text-muted">Isi keterangan (mis. "Bibit padi 50 kg") bila ada bantuan; kosongkan untuk menghapus.</small></div>
  </form>
</div>
@endsection
