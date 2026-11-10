@extends('admin.layout')
@section('title', 'SIPANDAI — Antrian')
@section('content')
<style>.aq .chip{display:inline-block;border-radius:20px;padding:4px 14px;margin:0 6px 6px 0;font-size:13px;font-weight:600}.aq td{vertical-align:middle!important}.aq tr.sedang{background:#eef3ff}</style>
<div class="aq">
  <div class="card"><div class="card-body d-flex flex-wrap align-items-center">
    <div class="mr-auto"><h5 class="mb-1" id="tgl">…</h5><div id="chips"></div><small class="text-muted"><span id="upd"></span> · <span>diperbarui otomatis tiap 15 detik</span></small></div>
    <button class="btn btn-primary btn-lg" id="panggil"><i class="fas fa-bullhorn"></i> Panggil antrean berikutnya</button>
  </div></div>
  <div id="pesan" class="alert" hidden></div>
  <div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>No. Registrasi</th><th>Pasien</th><th>Pemilik</th><th>Layanan</th><th>Waktu</th><th>Status</th><th style="width:190px">Aksi</th></tr></thead>
      <tbody id="tb"><tr><td colspan="7" class="text-center text-muted py-4">Memuat data…</td></tr></tbody>
    </table>
  </div></div>
</div>
<script>
(function () {
  var URL_D = @json(route('admin.sipandai.antrian.data')), URL_B = @json(route('admin.sipandai.antrian.berikut')), URL_S = @json(url('admin/sipandai/antrian')), TOKEN = @json(csrf_token());
  var ST = @json($status), LBL = {Terdaftar: T('Terdaftar'), Menunggu: T('Menunggu'), 'Sedang diperiksa': T('Sedang diperiksa'), Selesai: T('Selesai')};
  var WARNA = {Terdaftar: 'secondary', Menunggu: 'warning', 'Sedang diperiksa': 'info', Selesai: 'success'};
  var el = function (id) { return document.getElementById(id); }, sibuk = false;
  function post(url, body) { return fetch(url, {method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN}, body: JSON.stringify(body || {})}).then(function (r) { return r.json(); }); }
  function pesan(ok, m) { var p = el('pesan'); p.hidden = false; p.className = 'alert alert-' + (ok ? 'success' : 'warning'); p.textContent = m; }

  function render(d) {
    el('tgl').textContent = d.tanggal; el('upd').textContent = T('Diperbarui:') + ' ' + d.diperbarui;
    el('chips').innerHTML = ST.map(function (s) { return '<span class="chip badge-' + WARNA[s] + '" style="' + (WARNA[s] === 'warning' ? 'background:#ffc107;color:#212529' : '') + '">' + LBL[s] + ': ' + d.ringkas[s] + '</span>'; }).join('');
    var tb = el('tb'); tb.innerHTML = '';
    if (!d.rows.length) { tb.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">' + T('Belum ada antrean hari ini.') + '</td></tr>'; return; }
    d.rows.forEach(function (r) {
      var tr = document.createElement('tr'); if (r.status === 'Sedang diperiksa') tr.className = 'sedang';
      function td(t, html) { var c = document.createElement('td'); if (html) c.innerHTML = html; else c.textContent = t; tr.appendChild(c); return c; }
      var c1 = td(r.no); c1.style.fontFamily = 'monospace'; c1.style.fontWeight = '700'; var u = document.createElement('small'); u.className = 'text-muted'; u.textContent = ' (' + r.urutan + ')'; c1.appendChild(u);
      var c2 = td(''); var b = document.createElement('b'); b.textContent = r.hewan; c2.appendChild(b); var j = document.createElement('small'); j.className = 'text-muted'; j.textContent = ' · ' + r.jenis; c2.appendChild(j);
      td(r.pemilik); td(r.layanan); td(r.waktu);
      var cs = td(''); var sp = document.createElement('span'); sp.className = 'badge badge-' + WARNA[r.status]; sp.textContent = LBL[r.status] || r.status; cs.appendChild(sp);
      var ca = td(''); var sel = document.createElement('select'); sel.className = 'custom-select custom-select-sm'; sel.setAttribute('aria-label', T('Ubah status'));
      ST.forEach(function (s) { var o = document.createElement('option'); o.value = s; o.textContent = LBL[s]; if (s === r.status) o.selected = true; sel.appendChild(o); });
      sel.onchange = function () { post(URL_S + '/' + encodeURIComponent(r.uid) + '/status', {status: sel.value}).then(function (x) { if (!x.success) pesan(false, x.message); muat(); }); };
      ca.appendChild(sel); tb.appendChild(tr);
    });
  }
  function muat() {
    if (sibuk) return; sibuk = true;
    fetch(URL_D, {headers: {'Accept': 'application/json'}}).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(function (d) { sibuk = false; render(d); }).catch(function (e) { sibuk = false; pesan(false, T('Gagal memuat data:') + ' ' + e.message); });
  }
  el('panggil').onclick = function () { post(URL_B).then(function (x) { pesan(x.success, x.message); muat(); }); };
  muat(); setInterval(function () { if (!document.hidden && !document.activeElement.matches('select')) muat(); }, 15000);
})();
</script>
@endsection
