@extends('admin.layout')
@section('title', 'SIPANDAI — Data Pasien')
@section('content')
<style>
.rm .kb{border:1px solid #dbe5ff;border-radius:10px;padding:14px;margin-bottom:10px;background:#fff}.rm .kb.baru{border-left:5px solid #1F4FBF;background:#f4f7ff}
.rm summary{cursor:pointer;font-weight:600}.rm dt{font-weight:600;color:#6b7280;font-size:12px;margin-top:6px}.rm dd{margin:0}
</style>
<div class="card">
  <div class="card-header">
    @if(\App\Support\Akses::bisa(auth()->user(), 'sipandai_pendaftaran'))<a class="btn btn-primary btn-sm" href="{{ route('admin.sipandai.pendaftaran.tambah') }}"><i class="fas fa-plus"></i> Tambah pasien</a>@endif
    <form class="form-inline float-right" method="get"><input name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2" placeholder="Cari hewan / pemilik / No. registrasi / HP / NIK"><button class="btn btn-sm btn-secondary">Cari</button></form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped table-sm">
      <thead><tr><th>Registrasi</th><th>Nama Hewan</th><th>Jenis Hewan</th><th>Pemilik</th><th>Kontak</th><th>Kunjungan</th><th style="width:130px">Aksi</th></tr></thead>
      @forelse($rows as $p)
        <tr>
          <td><b style="font-family:monospace">{{ $p->no_pendaftaran }}</b><small class="d-block text-muted">{{ $p->tanggal->format('d-m-Y') }}</small></td>
          <td><b>{{ $p->nama_hewan }}</b></td><td>{{ $p->jenis_hewan }}@if($p->ras) <small class="text-muted">· {{ $p->ras }}</small>@endif</td>
          <td>{{ $p->nama_pemilik }}</td><td>{{ $p->no_hp }}</td>
          <td><span class="badge badge-primary">ke-{{ $p->kunjungan }}</span></td>
          <td><button type="button" class="btn btn-sm btn-outline-primary btn-rm" data-id="{{ $p->id }}"><i class="fas fa-notes-medical"></i> Rekam medis</button></td>
        </tr>
      @empty<tr><td colspan="7" class="text-center text-muted">Belum ada data pasien.</td></tr>@endforelse
    </table>
  </div>
  <div class="card-footer">{{ $rows->links('pagination::bootstrap-4') }}</div>
</div>

<div class="modal fade rm" id="rm" tabindex="-1" role="dialog" aria-labelledby="rmJudul" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="rmJudul">Rekam medis</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body" id="rmIsi"><p class="text-muted">Memuat data…</p></div>
  </div></div>
</div>
<script>
(function () {
  var URL_R = @json(url('admin/sipandai/pasien'));
  var el = function (id) { return document.getElementById(id); };
  var WARNA = {Terdaftar: 'secondary', Menunggu: 'warning', 'Sedang diperiksa': 'info', Selesai: 'success'};
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]; }); }
  function baris(l, v) { return v ? '<dt>' + l + '</dt><dd>' + esc(v) + '</dd>' : ''; }
  function kunjungan(k) {
    return '<dl class="mb-0">' + baris(T('Keluhan:'), k.keluhan) + baris(T('Info tambahan'), k.info) + baris(T('Diagnosa'), k.diagnosa) + baris(T('Jenis Tindakan'), k.tindakan) +
      baris(T('Catatan Pemeriksaan'), k.catatan) + baris(T('Petugas'), k.petugas) + baris(T('Waktu pemeriksaan'), k.waktuUpdate) + '</dl>' +
      (k.diagnosa ? '' : '<p class="text-muted small mb-0 mt-2">' + T('Belum ada hasil pemeriksaan dokter.') + '</p>');
  }
  function judul(k) {
    return esc(k.tanggal) + ' ' + esc(k.waktu) + ' · ' + esc(k.layanan) + ' <span class="badge badge-' + (WARNA[k.status] || 'secondary') + '">' + esc(k.label) + '</span> <small class="text-muted">' + esc(k.no) + '</small>';
  }
  function tampil(d) {
    var p = d.pasien, v = d.kunjungan, h = '<div class="mb-3"><h5 class="mb-0">' + esc(p.hewan) + ' <small class="text-muted">· ' + esc(p.jenis) + (p.ras ? ' · ' + esc(p.ras) : '') + ' · ' + esc(p.kelamin) + ' · ' + esc(p.umur) + '</small></h5>' +
      '<div class="small text-muted">' + T('Pemilik:') + ' ' + esc(p.pemilik) + ' · ' + esc(p.hp) + ' · ' + esc(p.alamat) + '</div><div class="small text-muted">' + p.jumlah + ' ' + T('kunjungan') + '</div></div>';
    if (v.length) {
      h += '<h6 class="text-primary">' + T('Kunjungan terbaru') + '</h6><div class="kb baru"><div class="mb-2">' + judul(v[0]) + '</div>' + kunjungan(v[0]) + '</div>';
      if (v.length > 1) {
        h += '<h6 class="text-muted mt-3">' + T('Riwayat kunjungan sebelumnya') + '</h6>';
        v.slice(1).forEach(function (k) { h += '<details class="kb"><summary>' + judul(k) + '</summary><div class="mt-2">' + kunjungan(k) + '</div></details>'; });
      }
    }
    el('rmIsi').innerHTML = h;
  }
  document.querySelectorAll('.btn-rm').forEach(function (b) {
    b.onclick = function () {
      el('rmIsi').innerHTML = '<p class="text-muted">' + T('Memuat data…') + '</p>';
      window.jQuery('#rm').modal('show');
      fetch(URL_R + '/' + b.dataset.id + '/riwayat', {headers: {'Accept': 'application/json'}}).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(tampil).catch(function (e) { el('rmIsi').innerHTML = '<div class="alert alert-danger">' + T('Gagal memuat data:') + ' ' + esc(e.message) + '</div>'; });
    };
  });
})();
</script>
@endsection
