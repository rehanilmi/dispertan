@extends('admin.layout')
@section('title', 'SIPANDAI — Dashboard Rekap')
@section('content')
<style>
.sp .box{background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 6px rgba(0,0,0,.07);margin-bottom:16px}
.sp .sum{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:16px}
.sp .sum div{background:#fff;border-radius:10px;padding:18px;border-left:5px solid #1F4FBF;box-shadow:0 1px 6px rgba(0,0,0,.07)}
.sp .sum small{color:#6b7280;font-weight:600}.sp .sum b{display:block;font-size:34px;line-height:1.1}
.sp .cw{position:relative;height:290px}.sp h3{font-size:16px;font-weight:700;margin:0}.sp .d{color:#6b7280;font-size:12px;margin-bottom:8px}
.sp .rk th,.sp .rk td{vertical-align:middle!important;white-space:nowrap}.sp .rk tbody td:nth-child(2){text-align:left}.sp .rk .tot{font-weight:700;background:#f4f7ff}
</style>
<div class="sp">
  <div class="d-flex align-items-center mb-3"><span class="text-muted small" id="upd">Memuat data…</span>
    <button class="btn btn-sm btn-outline-primary ml-auto" id="btnMuat"><i class="fas fa-sync-alt"></i> Muat ulang</button>
    <a class="btn btn-sm btn-primary ml-2" href="{{ route('admin.sipandai.pemeriksaan') }}"><i class="fas fa-stethoscope"></i> Buka pemeriksaan</a></div>
  <div id="err" class="alert alert-danger" hidden></div>
  <div class="sum"><div><small>Pendaftaran hari ini</small><b id="n1">0</b></div><div><small>Pendaftaran bulan ini</small><b id="n2">0</b></div><div><small>Total keseluruhan</small><b id="n3">0</b></div></div>

  <div class="box">
    <div class="d-flex flex-wrap align-items-center mb-2">
      <div><h3>Rekap pelayanan per bulan</h3><p class="d mb-0">Diambil dari data pendaftaran</p></div>
      <div class="form-inline ml-auto"><label for="thn" class="mr-2 small">Tahun</label><select id="thn" class="form-control form-control-sm mr-2"></select>
        <a id="xls" class="btn btn-sm btn-outline-success" href="#"><i class="fas fa-file-excel"></i> Unduh Excel</a></div>
    </div>
    <div class="table-responsive"><table class="table table-bordered table-sm text-center rk mb-2">
      <thead class="bg-light">
        <tr><th rowspan="3">No</th><th rowspan="3">Bulan</th><th rowspan="3">Pelayanan Umum</th><th rowspan="3">Vaksinasi Rabies</th><th colspan="4">Jenis Pelayanan Kesehatan Hewan</th><th rowspan="3">Verifikasi SKKH</th><th rowspan="3">Total Pelayanan</th></tr>
        <tr><th colspan="2">Sterilisasi</th><th colspan="2">Operasi</th></tr>
        <tr><th>OH ♀</th><th>Kastrasi ♂</th><th>Penjahitan Luka</th><th>Operasi Lainnya</th></tr>
      </thead>
      <tbody id="rkb"></tbody><tfoot id="rkf"></tfoot>
    </table></div>
    <p class="small text-muted mb-0">Keterangan: Pelayanan Umum = Konsultasi dan Pengobatan. Vaksinasi dihitung sebagai Vaksinasi Rabies. OH dan Kastrasi mengikuti jenis kelamin hewan pada layanan Sterilisasi. Penjahitan Luka dan Operasi Lainnya diambil dari tindakan yang dicentang dokter saat pemeriksaan; sebelum diperiksa, pendaftaran dihitung sebagai Pelayanan Umum.</p>
  </div>

  <div class="box"><h3>Tren pendaftaran per hari</h3><p class="d">14 hari terakhir</p><div class="cw"><canvas id="c1"></canvas></div></div>
  <div class="box"><h3>Tren pendaftaran per bulan</h3><p class="d">6 bulan terakhir</p><div class="cw"><canvas id="c2"></canvas></div></div>
  <div class="row"><div class="col-lg-7"><div class="box"><h3>Rekap per jenis pelayanan</h3><p class="d">Seluruh data dibandingkan dengan bulan ini</p><div class="cw"><canvas id="c3"></canvas></div></div></div>
  <div class="col-lg-5"><div class="box"><h3>Rekap per jenis hewan</h3><p class="d">Seluruh data</p><div class="cw"><canvas id="c4"></canvas></div></div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
  var URL = @json(route('admin.sipandai.data')), URL_B = @json(route('admin.sipandai.bulanan')), URL_X = @json(route('admin.sipandai.bulanan.export')), LOC = @json(app()->getLocale() === 'en' ? 'en-US' : 'id-ID'), ch = {};
  var C = {p: '#1F4FBF', ps: 'rgba(31,79,191,.18)', a: '#F2B93B', g: '#E4EAF8', t: '#5B6A8A'};
  var el = function (id) { return document.getElementById(id); };
  function pendek(iso) { var p = iso.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]).toLocaleDateString(LOC, {day: 'numeric', month: 'short'}); }
  function bulan(iso) { var p = iso.split('-'); return new Date(+p[0], +p[1] - 1, 1).toLocaleDateString(LOC, {month: 'short', year: 'numeric'}); }
  function tr(m) { return m; }
  function opt(legend, rot) {
    return {responsive: true, maintainAspectRatio: false, plugins: {legend: {display: !!legend, position: 'top', labels: {color: C.t}}},
      scales: {x: {grid: {display: false}, ticks: {color: C.t, autoSkip: false, maxRotation: rot ? 30 : 0}}, y: {beginAtZero: true, ticks: {color: C.t, precision: 0}, grid: {color: C.g}}}};
  }
  function gambar(id, cfg) { if (ch[id]) ch[id].destroy(); ch[id] = new Chart(el(id), cfg); }
  function render(d) {
    el('n1').textContent = d.totalHariIni; el('n2').textContent = d.totalBulanIni; el('n3').textContent = d.totalKeseluruhan;
    el('upd').textContent = T('Terakhir diperbarui:') + ' ' + new Date(d.updatedAt).toLocaleString(LOC, {dateStyle: 'long', timeStyle: 'short'});
    var LP = T('Pendaftaran');
    gambar('c1', {type: 'line', data: {labels: d.trendHarian.map(function (x) { return pendek(x.tanggal); }), datasets: [{label: LP, data: d.trendHarian.map(function (x) { return x.jumlah; }), borderColor: C.p, backgroundColor: C.ps, fill: true, tension: .3, pointRadius: 3}]}, options: opt(false)});
    gambar('c2', {type: 'bar', data: {labels: d.trendBulanan.map(function (x) { return bulan(x.bulan); }), datasets: [{label: LP, data: d.trendBulanan.map(function (x) { return x.jumlah; }), backgroundColor: C.p, borderRadius: 6, maxBarThickness: 40}]}, options: opt(false)});
    var lb = Object.keys(d.perLayananSemua), tl = lb.map(function (k) { return T(k); });
    gambar('c3', {type: 'bar', data: {labels: tl, datasets: [{label: T('Seluruh data'), data: lb.map(function (k) { return d.perLayananSemua[k]; }), backgroundColor: C.p, borderRadius: 5, maxBarThickness: 28}, {label: T('Bulan ini'), data: lb.map(function (k) { return d.perLayananBulanIni[k]; }), backgroundColor: C.a, borderRadius: 5, maxBarThickness: 28}]}, options: opt(true, true)});
    var hw = Object.keys(d.perHewanSemua);
    gambar('c4', {type: 'bar', data: {labels: hw.map(function (k) { return T(k); }), datasets: [{label: T('Jumlah'), data: hw.map(function (k) { return d.perHewanSemua[k]; }), backgroundColor: C.a, borderRadius: 5, maxBarThickness: 28}]}, options: opt(false, true)});
  }
  function muat() {
    fetch(URL, {headers: {'Accept': 'application/json'}}).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(function (d) { el('err').hidden = true; render(d); })
      .catch(function (e) { el('err').hidden = false; el('err').textContent = T('Gagal memuat data:') + ' ' + e.message; });
  }

  // ---- tabel rekap pelayanan per bulan ----
  var K = ['umum', 'rabies', 'oh', 'kastrasi', 'jahit', 'operasi', 'skkh'];
  function bulanan(th) {
    fetch(URL_B + '?tahun=' + (th || ''), {headers: {'Accept': 'application/json'}}).then(function (r) { return r.json(); }).then(function (d) {
      var s = el('thn'); if (!s.options.length || th === undefined) { s.innerHTML = ''; d.tahunList.forEach(function (y) { s.add(new Option(y, y, false, y === d.tahun)); }); }
      s.value = d.tahun; el('xls').href = URL_X + '?tahun=' + d.tahun;
      el('rkb').innerHTML = d.baris.map(function (b) {
        var kosong = b.total === 0;
        return '<tr><td>' + b.no + '</td><td>' + b.bulan + '</td>' + K.map(function (k) { return '<td>' + (kosong ? '' : b[k]) + '</td>'; }).join('') + '<td class="tot">' + b.total + '</td></tr>';
      }).join('');
      el('rkf').innerHTML = '<tr class="tot"><td></td><td class="text-left"><b>' + T('Total') + '</b></td>' + K.map(function (k) { return '<td class="tot">' + d.total[k] + '</td>'; }).join('') + '<td class="tot">' + d.total.total + '</td></tr>';
    });
  }
  el('thn').onchange = function () { bulanan(this.value); };
  el('btnMuat').onclick = function () { muat(); bulanan(el('thn').value || undefined); };
  muat(); bulanan();
  setInterval(function () { if (!document.hidden) { muat(); bulanan(el('thn').value || undefined); } }, 30000);
})();
</script>
@endsection
