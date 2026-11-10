@extends('admin.layout')
@section('title', 'Monitoring Antrian Puskeswan')
@section('content')
@php
  $warna = ['menunggu' => 'warning', 'dipanggil' => 'info', 'dilayani' => 'primary', 'selesai' => 'success', 'batal' => 'secondary', 'tidak_hadir' => 'danger'];
  $sedang = $semua->whereIn('status', ['dipanggil', 'dilayani'])->sortByDesc('dipanggil_at')->first();
  $maks = max(1, $minggu->max('n'));
@endphp
<form method="get" class="form-inline mb-3">
  <select name="lokasi" class="form-control mr-2" onchange="this.form.submit()">@foreach($daftar as $l)<option @selected($l === $lokasi)>{{ $l }}</option>@endforeach</select>
  <input type="date" name="tanggal" value="{{ $tgl }}" class="form-control mr-2" onchange="this.form.submit()">
  <span class="text-muted small">Halaman diperbarui otomatis tiap 15 detik</span>
</form>
<div class="row">
  <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>{{ $stat['menunggu'] }}</h3><p>Menunggu</p></div><div class="icon"><i class="fas fa-hourglass-half"></i></div></div></div>
  <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $stat['sedang'] }}</h3><p>Dipanggil / dilayani</p></div><div class="icon"><i class="fas fa-user-md"></i></div></div></div>
  <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>{{ $stat['selesai'] }}</h3><p>Selesai (batal/tidak hadir: {{ $stat['batal'] }})</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
  <div class="col-lg-3 col-6"><div class="small-box bg-secondary"><div class="inner"><h3>{{ $stat['total'] }}<small>/{{ $stat['kuota'] }}</small></h3><p>Terisi kuota · rata-rata {{ $stat['rata'] }} mnt/pasien</p></div><div class="icon"><i class="fas fa-users"></i></div></div></div>
</div>
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <div class="mr-auto"><span class="text-muted">Sedang dilayani:</span> <b style="font-size:1.6rem">{{ $sedang?->no ?? '—' }}</b> {{ $sedang?->nama }}</div>
        <form method="post" action="{{ route('admin.puskeswan.berikut') }}">@csrf<input type="hidden" name="lokasi" value="{{ $lokasi }}"><input type="hidden" name="tanggal" value="{{ $tgl }}"><button class="btn btn-success btn-lg"><i class="fas fa-bullhorn"></i> Panggil Berikutnya</button></form>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-sm table-hover">
          <thead><tr><th>No</th><th>Pemilik / Hewan</th><th>Layanan</th><th>Status</th><th style="width:230px">Aksi</th></tr></thead>
          @forelse($semua as $a)
            <tr class="{{ in_array($a->status, ['selesai','batal','tidak_hadir']) ? 'text-muted' : '' }}">
              <td><b>{{ $a->no }}</b></td>
              <td>{{ $a->nama }} <small>({{ $a->hp }})</small><br><small>{{ $a->jenis_hewan }} ×{{ $a->jumlah }} — {{ \Illuminate\Support\Str::limit($a->keluhan, 50) }}</small></td>
              <td>{{ $a->layanan }}</td><td><span class="badge badge-{{ $warna[$a->status] }}">{{ str_replace('_', ' ', $a->status) }}</span></td>
              <td>
                @php $tombol = ['menunggu' => [['panggil', 'Panggil', 'info'], ['tidak_hadir', 'Tidak hadir', 'danger']], 'dipanggil' => [['layani', 'Mulai', 'primary'], ['tidak_hadir', 'Tidak hadir', 'danger']], 'dilayani' => [['selesai', 'Selesai', 'success']]][$a->status] ?? []; @endphp
                @foreach($tombol as [$aksi, $lbl, $kls])<form class="d-inline" method="post" action="{{ route('admin.puskeswan.aksi', [$a, $aksi]) }}">@csrf<button class="btn btn-xs btn-{{ $kls }}">{{ $lbl }}</button></form> @endforeach
              </td>
            </tr>
          @empty<tr><td colspan="5" class="text-center text-muted">Belum ada antrian pada tanggal ini.</td></tr>@endforelse
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card"><div class="card-header"><h3 class="card-title">Jumlah antrian 7 hari terakhir</h3></div>
      <div class="card-body d-flex align-items-end" style="height:150px;gap:6px">@foreach($minggu as $m)<div class="text-center flex-fill"><small>{{ $m['n'] }}</small><div class="bg-info" style="height:{{ max(2, $m['n'] / $maks * 90) }}px"></div><small class="text-muted">{{ $m['tgl']->format('d/m') }}</small></div>@endforeach</div></div>
    <div class="card"><div class="card-header"><h3 class="card-title">Per jenis layanan</h3></div>
      <ul class="list-group list-group-flush">@forelse($perLayanan as $n => $c)<li class="list-group-item d-flex justify-content-between">{{ $n }}<span class="badge badge-primary">{{ $c }}</span></li>@empty<li class="list-group-item text-muted">-</li>@endforelse</ul></div>
  </div>
</div>
<script>setTimeout(function(){location.reload()},15000)</script>
@endsection
