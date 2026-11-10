@extends('admin.layout')
@section('title', 'SIPANDAI — Pendaftaran')
@section('content')
@php $warna = ['Terdaftar' => 'secondary', 'Menunggu' => 'warning', 'Sedang diperiksa' => 'info', 'Selesai' => 'success']; @endphp
@unless($adaLib)<div class="alert alert-danger">Library Excel belum terpasang. Jalankan di server: <code>composer require phpoffice/phpspreadsheet</code></div>@endunless
@foreach(session('hasil', []) as $h)
  <div class="alert alert-{{ $h['ok'] ? 'success' : 'danger' }}">
    <b>{{ $h['judul'] }}</b> — {{ $h['ringkas'] }}
    @if($h['err'])<ul class="mb-0 mt-2 small">@foreach(array_slice($h['err'], 0, 30) as $e)<li>{{ $e }}</li>@endforeach @if(count($h['err']) > 30)<li>… dan {{ count($h['err']) - 30 }} catatan lainnya</li>@endif</ul>@endif
  </div>
@endforeach
<div class="row">
  <div class="col-lg-4">
    <div class="card card-primary"><div class="card-header"><h3 class="card-title">Pendaftaran pasien</h3></div>
      <div class="card-body"><p class="small text-muted">Daftarkan pasien yang datang langsung ke Puskeswan. Formulirnya sama dengan layanan SIPANDAI di website (mendukung lebih dari satu hewan dan SKKH). Pasien yang didaftarkan petugas langsung berstatus <b>Menunggu</b>.</p>
        <a href="{{ route('admin.sipandai.pendaftaran.tambah') }}" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Tambah pasien</a></div></div>
  </div>
  <div class="col-lg-8">
    <div class="card card-warning"><div class="card-header"><h3 class="card-title">Impor pasien lama (Excel)</h3></div>
      <form method="post" action="{{ route('admin.sipandai.pendaftaran.impor') }}" enctype="multipart/form-data">@csrf
        <div class="card-body">
          <p class="small text-muted mb-2">Untuk memasukkan data pasien sebelum aplikasi ini ada. Baris pertama berisi nama kolom: <code>{{ implode(', ', array_slice($kolom, 1)) }}</code>. Kolom pertama (ID) boleh kosong dan diabaikan. Data dikenali dari <b>No_Pendaftaran + Urutan_Hewan</b>, jadi mengimpor ulang file yang sama memperbarui data, bukan menggandakan. Tanggal boleh berformat 2026-07-13 atau 13/07/2026.</p>
          <input type="file" name="files[]" multiple accept=".xlsx,.xls,.csv" required>@error('files.*')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="card-footer"><button class="btn btn-warning"><i class="fas fa-file-import"></i> Impor</button> <a href="{{ route('admin.sipandai.pendaftaran.template') }}" class="btn btn-link">Unduh template</a></div>
      </form></div>
  </div>
</div>
<div class="card">
  <div class="card-header">
    <form class="form-inline" method="get">
      <input name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2" placeholder="Cari No. Pendaftaran / pemilik / hewan / NIK">
      <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm mr-2">
      <button class="btn btn-sm btn-secondary">Cari</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped table-sm">
      <thead><tr><th>No. Registrasi</th><th>Tanggal</th><th>Pemilik</th><th>Hewan</th><th>Layanan</th><th>Status</th><th style="width:60px"></th></tr></thead>
      @forelse($rows as $p)
        <tr>
          <td><b style="font-family:monospace">{{ $p->no_pendaftaran }}</b> <small class="text-muted">({{ $p->urutan_hewan }})</small></td>
          <td>{{ $p->tanggal->format('d-m-Y') }} <small class="text-muted">{{ substr((string) $p->waktu, 0, 5) }}</small></td>
          <td>{{ $p->nama_pemilik }}<small class="d-block text-muted">{{ $p->no_hp }}</small></td>
          <td>{{ $p->nama_hewan }} <small class="text-muted">· {{ $p->jenis_hewan }}</small></td>
          <td>{{ $p->jenis_pelayanan }}</td>
          <td><span class="badge badge-{{ $warna[$p->status_pelayanan] ?? 'secondary' }}">{{ $p->status_pelayanan }}</span></td>
          <td>@if(\App\Support\Akses::bisaHapus(auth()->user()))<form method="post" action="{{ route('admin.sipandai.pendaftaran.hapus', $p->id) }}" onsubmit="return confirm('Hapus pendaftaran ini?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger" aria-label="Hapus"><i class="fas fa-trash"></i></button></form>@endif</td>
        </tr>
      @empty<tr><td colspan="7" class="text-center text-muted">Belum ada pendaftaran.</td></tr>@endforelse
    </table>
  </div>
  <div class="card-footer">{{ $rows->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
