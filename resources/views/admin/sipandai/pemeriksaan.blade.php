@extends('admin.layout')
@section('title', 'SIPANDAI — Pemeriksaan Dokter')
@section('content')
<style>
.pm .pc{background:#fff;border-radius:10px;padding:16px;margin-bottom:14px;box-shadow:0 1px 6px rgba(0,0,0,.07);border-left:5px solid #F2B93B}
.pm .pc.selesai{border-left-color:#1F4FBF}.pm .no{font-weight:700;font-family:monospace}.pm .st{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#FDF1CF;color:#92400e}
.pm .st.selesai{background:#DCE7FF;color:#173E9C}.pm .chip{display:inline-flex;align-items:center;gap:6px;border:1px solid #d5dade;border-radius:8px;padding:5px 10px;margin:0 6px 6px 0;font-size:13px;cursor:pointer}
.pm .chip:has(input:checked){background:#DCE7FF;border-color:#1F4FBF;font-weight:600}.pm .det p{margin:2px 0;font-size:14px}
</style>
<div class="pm">
  <div class="card"><div class="card-body">
    <div class="input-group"><input id="q" class="form-control" placeholder="Cari No. Pendaftaran / Nama Pemilik / Nama Hewan…"><div class="input-group-append"><button class="btn btn-primary" id="cari"><i class="fas fa-search"></i> Cari</button></div></div>
    <small class="text-muted">Kosongkan pencarian untuk menampilkan antrian hari ini yang belum selesai (Terdaftar, Menunggu, Sedang diperiksa).</small>
  </div></div>
  <div id="muat" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i> Memuat data pasien…</div>
  <div id="err" class="alert alert-danger" hidden></div>
  <div id="kosong" class="alert alert-light border" hidden>Tidak ada data yang cocok. Coba kata kunci lain, atau semua antrian hari ini sudah selesai diperiksa.</div>
  <div id="daftar"></div>
</div>
<script>
(function () {
  var TINDAKAN = @json($tindakan), URL_CARI = @json(route('admin.sipandai.cari')), URL_SIMPAN = @json(url('admin/sipandai/pemeriksaan')), TOKEN = @json(csrf_token());
  var L = {pemilik: T('Pemilik:'), pel: T('Pelayanan:'), kel: T('Keluhan:'), hewan: T('Hewan'), diag: T('Diagnosa'), tind: T('Jenis Tindakan'), lain: T('Tindakan lain (opsional, pisahkan dengan koma)'),
    cat: T('Catatan Pemeriksaan'), opsi: T('(opsional)'), phDiag: T('Tuliskan diagnosa dokter'), phCat: T('Catatan tambahan, mis. anjuran kontrol ulang'),
    simpan: T('Simpan Pemeriksaan'), isi: T('Isi Pemeriksaan'), ubah: T('Ubah Pemeriksaan'), tutup: T('Tutup Form'), menyimpan: T('Menyimpan…')};
  var ST = {Terdaftar: T('Terdaftar'), Menunggu: T('Menunggu'), 'Sedang diperiksa': T('Sedang diperiksa'), Selesai: T('Selesai')};
  var el = function (id) { return document.getElementById(id); };
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]; }); }
  function tampil(a) { el('muat').hidden = !a.muat; el('err').hidden = !a.err; el('kosong').hidden = !a.kosong; if (a.err) el('err').textContent = a.err; }

  function kartu(it) {
    var pilih = (it.jenisTindakan || '').split(',').map(function (s) { return s.trim(); }), lain = pilih.filter(function (t) { return t && TINDAKAN.indexOf(t) === -1; });
    var selesai = it.statusPelayanan === 'Selesai';
    var d = document.createElement('div'); d.className = 'pc' + (selesai ? ' selesai' : '');
    d.innerHTML =
      '<div class="d-flex justify-content-between"><div><div class="no">' + esc(it.noPendaftaran) + ' <small class="text-muted">(' + L.hewan + ' ' + esc(it.urutanHewan) + ')</small></div>' +
      '<div style="font-size:17px;font-weight:700">' + esc(it.namaHewan) + ' <small class="text-muted">· ' + esc(it.jenisHewan) + '</small></div></div><span class="st' + (selesai ? ' selesai' : '') + '">' + esc(ST[it.statusPelayanan] || it.statusPelayanan) + '</span></div>' +
      '<div class="det mt-2"><p><b>' + L.pemilik + '</b> ' + esc(it.namaPemilik) + '</p><p><b>' + L.pel + '</b> ' + esc(it.jenisPelayanan) + '</p><p><b>' + L.kel + '</b> ' + esc(it.keluhanUtama) + '</p></div>' +
      '<button class="btn btn-sm btn-outline-primary mt-2 tg">' + (selesai ? L.ubah : L.isi) + '</button>' +
      '<div class="fm mt-3" hidden>' +
        '<div class="form-group"><label>' + L.diag + ' <span class="text-danger">*</span></label><textarea class="form-control dg" rows="3" placeholder="' + esc(L.phDiag) + '"></textarea><div class="text-danger small e-dg"></div></div>' +
        '<div class="form-group"><label>' + L.tind + ' <span class="text-danger">*</span></label><div class="tk">' +
          TINDAKAN.map(function (o) { return '<label class="chip"><input type="checkbox" value="' + esc(o) + '"' + (pilih.indexOf(o) > -1 ? ' checked' : '') + '>' + esc(o) + '</label>'; }).join('') +
        '</div><input class="form-control tl" placeholder="' + esc(L.lain) + '"><div class="text-danger small e-tk"></div></div>' +
        '<div class="form-group"><label>' + L.cat + ' <small class="text-muted">' + L.opsi + '</small></label><textarea class="form-control ct" rows="2" placeholder="' + esc(L.phCat) + '"></textarea></div>' +
        '<button class="btn btn-primary sv">' + L.simpan + '</button></div>';
    d.querySelector('.dg').value = it.diagnosa || ''; d.querySelector('.ct').value = it.catatanPemeriksaan || ''; d.querySelector('.tl').value = lain.join(', ');
    var fm = d.querySelector('.fm'), tg = d.querySelector('.tg');
    tg.onclick = function () { fm.hidden = !fm.hidden; tg.textContent = fm.hidden ? (d.classList.contains('selesai') ? L.ubah : L.isi) : L.tutup; };
    d.querySelector('.sv').onclick = function () { simpan(it.idPendaftaran, d, tg, fm); };
    return d;
  }

  function simpan(id, d, tg, fm) {
    var dg = d.querySelector('.dg').value.trim(), ct = d.querySelector('.ct').value.trim(), tl = d.querySelector('.tl').value.trim();
    var t = [].map.call(d.querySelectorAll('.tk input:checked'), function (c) { return c.value; });
    tl.split(',').forEach(function (x) { x = x.trim(); if (x) t.push(x); });
    d.querySelector('.e-dg').textContent = dg ? '' : T('Diagnosa wajib diisi.');
    d.querySelector('.e-tk').textContent = t.length ? '' : T('Pilih minimal 1 jenis tindakan.');
    if (!dg || !t.length) return;
    var b = d.querySelector('.sv'); b.disabled = true; b.textContent = L.menyimpan;
    fetch(URL_SIMPAN + '/' + encodeURIComponent(id), {method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN},
      body: JSON.stringify({diagnosa: dg, jenisTindakan: t.join(', '), catatan: ct})})
      .then(function (r) { return r.json(); })
      .then(function (r) {
        b.disabled = false; b.textContent = L.simpan;
        if (r && r.success) { d.classList.add('selesai'); var s = d.querySelector('.st'); s.textContent = ST.Selesai; s.classList.add('selesai'); fm.hidden = true; tg.textContent = L.ubah; }
        else alert((r && r.message) || T('Gagal menyimpan pemeriksaan.'));
      }).catch(function () { b.disabled = false; b.textContent = L.simpan; alert(T('Terjadi kesalahan sistem.')); });
  }

  function muat(kw) {
    tampil({muat: true}); el('daftar').innerHTML = '';
    fetch(URL_CARI + '?q=' + encodeURIComponent(kw || ''), {headers: {'Accept': 'application/json'}})
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(function (r) {
        if (!r || r.success !== true) return tampil({err: (r && r.message) || T('Data pemeriksaan tidak dapat dimuat.')});
        tampil({kosong: !r.data.length}); r.data.forEach(function (it) { el('daftar').appendChild(kartu(it)); });
      }).catch(function (e) { tampil({err: T('Gagal mengambil data pasien:') + ' ' + e.message}); });
  }
  el('cari').onclick = function () { muat(el('q').value); };
  el('q').addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); muat(el('q').value); } });
  muat('');
})();
</script>
@endsection
