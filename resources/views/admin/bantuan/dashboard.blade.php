@extends('admin.layout')
@section('title', 'Monitoring Bantuan Kelompok Tani')
@section('content')
<style>
.mon .kartu{background:#fff;border-radius:12px;padding:18px;margin-bottom:16px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.mon h2{font-size:17px;font-weight:700;margin:0 0 4px}.mon .ket{color:#6b7280;font-size:12.5px;margin-bottom:10px}
.mon .top{display:flex;gap:24px;align-items:center;flex-wrap:wrap;background:linear-gradient(135deg,#14532d,#1e4a3a);color:#fff;border-radius:14px;padding:22px;margin-bottom:16px}
.mon .top .tx{flex:1;min-width:260px}.mon .top p{margin:6px 0;opacity:.9}.mon .stamp{font-size:11px;opacity:.7}
.mon .ring{position:relative;width:150px;height:150px}.mon .ring .tengah{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}
.mon .ring b{font-size:28px}.mon .ring span{font-size:10px;opacity:.8;max-width:90px}
.mon .kpi{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:16px}
.mon .k{background:#fff;border-radius:12px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);border-top:4px solid #3E7CB1}
.mon .k.b{border-color:#8A5CB8}.mon .k.r{border-color:#C2453D}.mon .k.g{border-color:#2FA06A}
.mon .k b{display:block;font-size:30px}.mon .k span{font-size:12px;color:#6b7280}
.mon .k i{display:block;height:5px;background:#eef0f2;border-radius:4px;margin-top:8px;overflow:hidden}.mon .k u{display:block;height:100%;background:#2FA06A;transition:width .9s}
.mon .tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px}
.mon .tab-btn{border:0;background:#e5e9ec;padding:8px 16px;border-radius:20px;font-weight:600;font-size:13px;cursor:pointer}
.mon .tab-btn.aktif{background:#14532d;color:#fff}.mon .panel{display:none}.mon .panel.aktif{display:block}
.mon .nb{background:#C2453D;color:#fff;border-radius:10px;padding:0 7px;font-size:11px;margin-left:4px}.mon .nb.ok{background:#2FA06A}
.mon .cb{position:relative;height:300px}.mon .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.mon .scroll{overflow-x:auto}
.mon table{width:100%;border-collapse:collapse;font-size:13px}.mon th{background:#f3f5f6;text-align:left;padding:8px 10px;font-size:12px}
.mon td{padding:7px 10px;border-top:1px solid #eef0f2}.mon .heat td,.mon .heat th{text-align:center}.mon .heat td:first-child,.mon .heat th:first-child{text-align:left}
.mon tfoot td{font-weight:700;background:#f3f5f6}.mon tbody tr[data-i]{cursor:pointer}.mon tbody tr[data-i]:hover{background:#f0f7f3}
.mon small{display:block;color:#8a949b;font-size:11px}
.mon .bar{display:flex;align-items:center;gap:6px}.mon .bar i{flex:1;height:8px;background:#e5e9ec;border-radius:4px;overflow:hidden;min-width:60px}.mon .bar u{display:block;height:100%;background:#2FA06A}
.mon .filter{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px}.mon .filter input,.mon .filter select{border:1px solid #d5dade;border-radius:8px;padding:6px 10px;font-size:13px}
.mon .btn2{border:1px solid #d5dade;background:#fff;border-radius:8px;padding:6px 12px;font-size:13px;cursor:pointer}.mon .btn2.gold{background:#F2B73D;border-color:#F2B73D;font-weight:700}
.mon .btn2:disabled{opacity:.4}
.mon .thn{display:flex;gap:3px;flex-wrap:wrap}.mon .t{font-size:10px;padding:2px 6px;border-radius:5px;background:#eef0f2;color:#9aa3a9}.mon .t.on{background:#2FA06A;color:#fff}
.mon .page{display:flex;gap:12px;align-items:center;justify-content:center;margin-top:12px}
.mon .grup h3{font-size:15px;margin:14px 0 6px}.mon .pill{background:#eef0f2;border-radius:10px;padding:2px 10px;font-size:11px}
.mon .pr{display:flex;justify-content:space-between;gap:10px;padding:8px 10px;border-top:1px solid #eef0f2;cursor:pointer}.mon .pr:hover{background:#f0f7f3}.mon .pr small{display:inline;margin-left:6px}
.mon .skor{display:flex;gap:18px;align-items:center;margin-bottom:12px}.mon .skor b{font-size:38px;color:#14532d}
.mon .qgrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px}
.mon .q{background:#fdecea;border-radius:10px;padding:12px}.mon .q.ok{background:#e4f2ea}.mon .q b{font-size:22px;display:block}.mon .q span{font-size:12px}
.mon .mb{background:#fdecea;color:#b3392f;border-radius:6px;padding:2px 8px;font-size:11px;margin-right:4px}
.mon .kosong{text-align:center;color:#8a949b;padding:24px}
.drawer{position:fixed;inset:0;background:rgba(0,0,0,.4);display:none;z-index:1100}.drawer.buka{display:block}
.drawer-in{position:absolute;right:0;top:0;bottom:0;width:min(420px,100%);background:#fff;padding:22px;overflow-y:auto}
.drawer .dl{display:grid;grid-template-columns:120px 1fr;gap:4px 10px;font-size:13px;margin:14px 0}.drawer dt{color:#6b7280;font-weight:500}.drawer dd{margin:0}
.drawer .tl>div{border-left:3px solid #d5dade;padding:4px 0 8px 10px;font-size:13px}.drawer .tl>div.on{border-color:#2FA06A}
.drawer .src{color:#fff;border-radius:5px;padding:1px 7px;font-size:10px;margin-right:6px}.drawer .btn2{border:1px solid #d5dade;background:#fff;border-radius:8px;padding:6px 12px;cursor:pointer}
@media(max-width:900px){.mon .grid2{grid-template-columns:1fr}}
</style>
<div class="mon">
  <div class="top">
    <div class="tx">
      <p style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;margin:0">Dinas Pertanian Kota Padang</p>
      <h1 style="font-size:26px;margin:4px 0">Monitoring Bantuan Kelompok Tani</h1>
      <p id="narasi">Memuat…</p>
      <button class="btn2 gold" onclick="location.reload()">Muat ulang</button>
      @if(\App\Support\Akses::bisa(auth()->user(), 'kelompok'))<a class="btn2" style="color:#14532d" href="{{ route('admin.kelompok.index') }}">Kelola data &amp; impor Excel</a>@endif
      <p class="stamp" id="stamp"></p>
    </div>
    <div class="ring"><canvas id="cRing" width="150" height="150"></canvas><div class="tengah"><b id="ringPct">–</b><span>kelompok sudah dibantu</span></div></div>
  </div>
  <section class="kpi" id="kpi"></section>
  <nav class="tabs">
    <button class="tab-btn aktif" data-tab="ringkasan">Ringkasan</button><button class="tab-btn" data-tab="peta">Peta Kecamatan</button>
    <button class="tab-btn" data-tab="data">Data kelompok</button><button class="tab-btn" data-tab="prioritas">Daftar prioritas</button>
    <button class="tab-btn" data-tab="kualitas">Kualitas data <span class="nb" id="nbKualitas">0</span></button>
  </nav>

  <section class="panel aktif" id="panel-ringkasan">
    <div class="kartu"><h2>Kurva cakupan kumulatif</h2><p class="ket">Persentase kelompok yang sudah pernah dibantu sampai akhir tiap tahun (garis), dan jumlah kelompok yang pertama kali tersentuh di tahun itu (batang).</p><div class="cb"><canvas id="cKum"></canvas></div></div>
    <div class="grid2">
      <div class="kartu"><h2>Kelompok dibantu per tahun</h2><p class="ket">Dipecah berdasarkan sumber dana</p><div class="cb"><canvas id="cTahun"></canvas></div></div>
      <div class="kartu"><h2>Cakupan per kecamatan</h2><p class="ket">Sudah dan belum pernah dibantu</p><div class="cb"><canvas id="cKec"></canvas></div></div>
    </div>
  </section>
  <section class="panel" id="panel-peta"><div class="kartu"><h2>Peta Kecamatan × tahun</h2><p class="ket">Angka di kolom tahun = jumlah kelompok dibantu. Warna makin pekat = cakupan makin tinggi.</p><div class="scroll"><table class="heat" id="heat"></table></div></div></section>
  <section class="panel" id="panel-data"><div class="kartu">
    <div class="filter">
      <input id="fCari" type="search" placeholder="Cari nama kelompok tani…">
      <select id="fKec"><option value="">Semua kecamatan</option></select>
      <select id="fStatus"><option value="">Semua status</option><option value="sudah">Sudah dibantu</option><option value="belum">Belum dibantu</option></select>
      <select id="fTahun"><option value="">Semua tahun</option></select>
      <button class="btn2" id="btnCsvData">Unduh CSV</button><button class="btn2" id="btnXlsData">Unduh Excel</button>
    </div>
    <p class="ket" id="hasil"></p>
    <div class="scroll"><table><thead><tr><th>Kecamatan</th><th>Kelompok tani</th><th>Anggota</th><th>Ketua</th><th>Riwayat bantuan</th></tr></thead><tbody id="tbody"></tbody></table></div>
    <div class="page"><button class="btn2" id="prev">Sebelumnya</button><span id="pinfo"></span><button class="btn2" id="next">Berikutnya</button></div>
  </div></section>
  <section class="panel" id="panel-prioritas"><div class="kartu">
    <div class="filter"><select id="fPrio"><option value="belum">Belum pernah dibantu</option><option value="lama"></option></select><button class="btn2" id="btnCsvPrio">Unduh CSV</button></div>
    <p class="ket" id="prioInfo"></p><div id="prioList"></div>
  </div></section>
  <section class="panel" id="panel-kualitas">
    <div class="kartu"><h2>Pemeriksa kualitas data</h2><p class="ket">Perbaiki temuan lewat menu Kelompok Tani, lalu muat ulang halaman untuk memeriksa lagi.</p><div class="skor" id="skor"></div><div class="qgrid" id="qgrid"></div></div>
    <div class="kartu"><div class="filter"><select id="fQ"><option value="">Semua masalah</option></select><button class="btn2" id="btnCsvQ">Unduh CSV</button></div>
      <p class="ket" id="qInfo"></p><div class="scroll"><table><thead><tr><th>Kecamatan</th><th>Kelompok tani</th><th>Masalah</th></tr></thead><tbody id="qBody"></tbody></table></div></div>
  </section>
</div>
<div class="drawer" id="drawer"><div class="drawer-in" id="drawerIn"></div></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
'use strict';
var DATA = {!! \Illuminate\Support\Js::from($data) !!}, P = 1, PER = 25, charts = {};
var WARNA = {'APBD Kota':'#3E7CB1','APBD Provinsi':'#2FA06A','APBN':'#8A5CB8'};
var $ = function (id) { return document.getElementById(id); };
function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
function num(n) { return Number(n).toLocaleString('id-ID'); }
function adaTahun(g, y) { return g.bantuan[y] && g.bantuan[y].ada; }
var BATAS = DATA.batasLama;
$('fPrio').options[1].text = 'Terakhir dibantu ' + BATAS + ' atau lebih lama';

function render() {
  var S = DATA.summary, G = DATA.groups, anggota = 0, anggotaSudah = 0;
  G.forEach(function (g) { anggota += g.jumlahAnggota; if (g.pernahDibantu) anggotaSudah += g.jumlahAnggota; });
  var berulang = G.filter(function (g) { return g.jumlahTahunDibantu >= 3; }).length;
  $('stamp').textContent = 'Sumber: database Kelompok Tani · diperbarui ' + new Date(DATA.generatedAt).toLocaleString('id-ID');
  $('ringPct').textContent = S.persenSudah + '%';
  $('narasi').innerHTML = '<b>' + num(S.totalSudahDibantu) + '</b> dari <b>' + num(S.totalKelompok) + '</b> kelompok tani di <b>' + S.jumlahKecamatan + ' kecamatan</b> pernah menerima bantuan. Masih ada <b>' + num(S.totalBelumDibantu) + '</b> kelompok yang belum tersentuh.';
  $('kpi').innerHTML =
    kpi('', S.totalKelompok, 'Total kelompok tani', 100) +
    kpi('b', anggota, 'Total anggota (' + num(anggotaSudah) + ' terjangkau)', anggota ? anggotaSudah / anggota * 100 : 0) +
    kpi('r', S.totalBelumDibantu, 'Belum pernah dibantu', 100 - S.persenSudah) +
    kpi('g', berulang, 'Dibantu 3 tahun atau lebih', S.totalKelompok ? berulang / S.totalKelompok * 100 : 0);
  countUp(); kualitas(); gambar(S, G); heat(G); isi(); tabel(); prioritas();
}
function kpi(c, a, l, p) { return '<div class="k ' + c + '"><b data-n="' + a + '">0</b><span>' + l + '</span><i><u style="width:0" data-w="' + (p || 0) + '"></u></i></div>'; }
function countUp() {
  document.querySelectorAll('.k b').forEach(function (e) {
    var n = +e.dataset.n, t0 = performance.now();
    (function f(t) { var p = Math.min(1, (t - t0) / 900); e.textContent = num(Math.round(n * (1 - Math.pow(1 - p, 3)))); if (p < 1) requestAnimationFrame(f); })(t0);
  });
  setTimeout(function () { document.querySelectorAll('.k u').forEach(function (u) { u.style.width = u.dataset.w + '%'; }); }, 50);
}
function chart(id, cfg) { if (charts[id]) charts[id].destroy(); charts[id] = new Chart($(id), cfg); }
function gambar(S, G) {
  Chart.defaults.font.family = "'Inter',sans-serif"; Chart.defaults.color = '#5C7167';
  var first = {}, kum = 0, baru = [], pct = [];
  G.forEach(function (g) { for (var i = 0; i < DATA.years.length; i++) if (adaTahun(g, DATA.years[i])) { first[DATA.years[i]] = (first[DATA.years[i]] || 0) + 1; break; } });
  DATA.years.forEach(function (y) { var n = first[y] || 0; kum += n; baru.push(n); pct.push(G.length ? Math.round(kum / G.length * 1000) / 10 : 0); });
  chart('cKum', {data:{labels:DATA.years, datasets:[
    {type:'line', label:'Cakupan kumulatif (%)', data:pct, yAxisID:'y1', borderColor:'#1F7A4D', backgroundColor:'rgba(61,190,132,.18)', fill:true, tension:.35, borderWidth:3, pointRadius:5, pointBackgroundColor:'#fff', pointBorderWidth:3, order:1},
    {type:'bar', label:'Kelompok baru terjangkau', data:baru, yAxisID:'y', backgroundColor:'#F2B73D', borderRadius:8, order:2}]},
    options:{maintainAspectRatio:false, interaction:{mode:'index', intersect:false}, scales:{y:{beginAtZero:true, title:{display:true, text:'Kelompok baru'}, grid:{display:false}}, y1:{position:'right', min:0, max:100, ticks:{callback:function (v) { return v + '%'; }}}, x:{grid:{display:false}}}, plugins:{legend:{position:'bottom'}}}});
  chart('cRing', {type:'doughnut', data:{datasets:[{data:[S.totalSudahDibantu, S.totalBelumDibantu], backgroundColor:['#F2B73D','#0f3a2b'], borderWidth:0}]}, options:{cutout:'78%', plugins:{legend:{display:false}, tooltip:{enabled:false}}, animation:{duration:900}}});
  var ds = Object.keys(WARNA).map(function (s) {
    return {label:s, backgroundColor:WARNA[s], data:DATA.years.map(function (y) { return G.filter(function (g) { return g.bantuan[y].detail.some(function (d) { return d.sumber === s; }); }).length; })};
  });
  chart('cTahun', {type:'bar', data:{labels:DATA.years, datasets:ds}, options:{maintainAspectRatio:false, scales:{x:{stacked:true, grid:{display:false}}, y:{stacked:true, beginAtZero:true}}, plugins:{legend:{position:'bottom'}}}});
  var K = S.perKecamatan;
  chart('cKec', {type:'bar', data:{labels:K.map(function (k) { return k.kecamatan; }), datasets:[{label:'Sudah dibantu', backgroundColor:'#1F7A4D', data:K.map(function (k) { return k.sudah; })}, {label:'Belum dibantu', backgroundColor:'#C2453D', data:K.map(function (k) { return k.belum; })}]}, options:{indexAxis:'y', maintainAspectRatio:false, scales:{x:{stacked:true}, y:{stacked:true, grid:{display:false}}}, plugins:{legend:{position:'bottom'}}}});
}
function heat(G) {
  var h = '<thead><tr><th>Kecamatan</th>' + DATA.years.map(function (y) { return '<th>' + y + '</th>'; }).join('') + '<th>Total kelompok</th><th>Belum dibantu</th><th>Persentase dibantu</th></tr></thead><tbody>';
  var tt = 0, tb = 0, ty = {};
  DATA.summary.perKecamatan.forEach(function (k) {
    var rows = G.filter(function (g) { return g.kecamatan === k.kecamatan; }), p = Math.round(k.sudah / k.total * 1000) / 10;
    tt += k.total; tb += k.belum;
    h += '<tr><td>' + esc(k.kecamatan) + '</td>' + DATA.years.map(function (y) {
      var n = rows.filter(function (g) { return adaTahun(g, y); }).length, a = n / rows.length; ty[y] = (ty[y] || 0) + n;
      return '<td style="background:rgba(31,122,77,' + (n ? (0.12 + a * 0.75).toFixed(2) : 0.04) + ');color:' + (a > .5 ? '#fff' : 'inherit') + '">' + n + '</td>';
    }).join('') + '<td><b>' + k.total + '</b></td><td style="color:#D14B41;font-weight:700">' + k.belum + '</td><td><div class="bar"><i><u style="width:' + p + '%"></u></i><b>' + p + '%</b></div></td></tr>';
  });
  h += '</tbody><tfoot><tr><td>Kota Padang</td>' + DATA.years.map(function (y) { return '<td>' + (ty[y] || 0) + '</td>'; }).join('') + '<td>' + tt + '</td><td style="color:#D14B41">' + tb + '</td><td>' + (tt ? Math.round((tt - tb) / tt * 1000) / 10 : 0) + '%</td></tr></tfoot>';
  $('heat').innerHTML = h;
}
function isi() {
  if ($('fKec').options.length > 1) return;
  DATA.summary.perKecamatan.slice().sort(function (a, b) { return a.kecamatan.localeCompare(b.kecamatan); }).forEach(function (k) { $('fKec').add(new Option(k.kecamatan, k.kecamatan)); });
  DATA.years.slice().reverse().forEach(function (y) { $('fTahun').add(new Option(y, y)); });
}
function filtered() {
  var q = $('fCari').value.trim().toLowerCase(), kc = $('fKec').value, st = $('fStatus').value, th = $('fTahun').value;
  return DATA.groups.filter(function (g) {
    return (!kc || g.kecamatan === kc) && (!q || g.poktan.toLowerCase().indexOf(q) > -1) && (!th || adaTahun(g, th)) && (st !== 'sudah' || g.pernahDibantu) && (st !== 'belum' || !g.pernahDibantu);
  });
}
function tabel() {
  var d = filtered(), tp = Math.max(1, Math.ceil(d.length / PER));
  P = Math.min(Math.max(P, 1), tp);
  $('hasil').textContent = num(d.length) + ' kelompok ditemukan. Klik baris untuk melihat riwayat lengkap.';
  $('pinfo').textContent = 'Halaman ' + P + ' dari ' + tp; $('prev').disabled = P <= 1; $('next').disabled = P >= tp;
  $('tbody').innerHTML = d.length ? d.slice((P - 1) * PER, P * PER).map(function (g) {
    return '<tr data-i="' + DATA.groups.indexOf(g) + '"><td>' + esc(g.kecamatan) + '</td><td><b>' + esc(g.poktan) + '</b><small>' + esc(g.desa) + '</small></td><td>' + g.jumlahAnggota + '</td><td>' + esc(g.ketua) + '</td><td><div class="thn">' +
      DATA.years.map(function (y) { return '<span class="t' + (adaTahun(g, y) ? ' on' : '') + '">' + y + '</span>'; }).join('') + '</div></td></tr>';
  }).join('') : '<tr><td colspan="5" class="kosong">Tidak ada kelompok yang cocok.</td></tr>';
}
function prioData() {
  var m = $('fPrio').value;
  return DATA.groups.filter(function (g) { return m === 'belum' ? !g.pernahDibantu : g.pernahDibantu && Number(g.tahunTerakhirDibantu) <= BATAS; });
}
function prioritas() {
  var d = prioData(), grp = {};
  $('prioInfo').textContent = num(d.length) + ' kelompok. Diurutkan dari kecamatan dengan kelompok terbanyak, lalu anggota terbanyak.';
  d.forEach(function (g) { (grp[g.kecamatan] = grp[g.kecamatan] || []).push(g); });
  var ks = Object.keys(grp).sort(function (a, b) { return grp[b].length - grp[a].length; });
  $('prioList').innerHTML = ks.length ? ks.map(function (k) {
    return '<div class="grup"><h3>' + esc(k) + ' <span class="pill">' + grp[k].length + ' kelompok</span></h3>' +
      grp[k].sort(function (a, b) { return b.jumlahAnggota - a.jumlahAnggota; }).map(function (g) {
        return '<div class="pr" data-i="' + DATA.groups.indexOf(g) + '"><b>' + esc(g.poktan) + '<small>' + esc(g.desa) + '</small></b><span>' + esc(g.ketua) + ' · ' + g.jumlahAnggota + ' anggota</span></div>'; }).join('') + '</div>';
  }).join('') : '<div class="kosong">Tidak ada kelompok pada kategori ini.</div>';
}
function detail(i) {
  var g = DATA.groups[i];
  $('drawerIn').innerHTML = '<button class="btn2" id="tutup">Tutup</button> ' + (g.editUrl ? '<a class="btn2" href="' + esc(g.editUrl) + '">Ubah data &amp; bantuan</a>' : '') +
    '<h3 style="margin-top:16px">' + esc(g.poktan) + '</h3><span class="pill">' + (g.pernahDibantu ? 'Dibantu ' + g.jumlahTahunDibantu + ' tahun (terakhir ' + g.tahunTerakhirDibantu + ')' : 'Belum pernah dibantu') + '</span>' +
    '<dl class="dl"><dt>Kecamatan</dt><dd>' + esc(g.kecamatan) + '</dd><dt>Desa/Kelurahan</dt><dd>' + esc(g.desa) + '</dd><dt>ID Poktan</dt><dd>' + esc(g.idPoktan) + '</dd><dt>Ketua</dt><dd>' + esc(g.ketua) + '</dd><dt>Anggota</dt><dd>' + g.jumlahAnggota + '</dd><dt>Alamat</dt><dd>' + esc(g.alamat) + '</dd></dl><h3>Riwayat bantuan</h3><div class="tl">' +
    DATA.years.slice().reverse().map(function (y) {
      var b = g.bantuan[y];
      return '<div class="' + (b.ada ? 'on' : '') + '"><b>' + y + '</b><br>' + (b.ada ? b.detail.map(function (d) { return '<span class="src" style="background:' + (WARNA[d.sumber] || '#888') + '">' + esc(d.sumber) + '</span>' + esc(d.keterangan); }).join('<br>') : '<span style="color:#8a949b">Tidak ada bantuan</span>') + '</div>';
    }).join('') + '</div>';
  $('drawer').classList.add('buka'); $('tutup').focus();
}
var MASALAH = {ganda:'Nama kelompok ganda', id:'ID kosong', ketua:'Ketua kosong', anggota:'Anggota bernilai 0', kec:'Kecamatan kosong'}, QLIST = [];
function kualitas() {
  var cnt = {}, seen = {};
  DATA.groups.forEach(function (g) { var k = g.kecamatan + '|' + g.poktan.toLowerCase().replace(/\s+/g, ' '); seen[k] = (seen[k] || 0) + 1; });
  QLIST = [];
  DATA.groups.forEach(function (g) {
    var m = [], k = g.kecamatan + '|' + g.poktan.toLowerCase().replace(/\s+/g, ' ');
    if (seen[k] > 1) m.push('ganda');
    if (!g.idPoktan || g.idPoktan === '-') m.push('id');
    if (!g.ketua || g.ketua === '-') m.push('ketua');
    if (!Number(g.jumlahAnggota)) m.push('anggota');
    if (g.kecamatan === 'Tanpa Kecamatan') m.push('kec');
    m.forEach(function (x) { cnt[x] = (cnt[x] || 0) + 1; });
    if (m.length) QLIST.push({g:g, m:m});
  });
  var bersih = DATA.groups.length - QLIST.length, sk = DATA.groups.length ? Math.round(bersih / DATA.groups.length * 1000) / 10 : 100;
  $('skor').innerHTML = '<b>' + sk + '%</b><p>' + num(bersih) + ' dari ' + num(DATA.groups.length) + ' baris data sudah lengkap dan konsisten.<br>' + (QLIST.length ? num(QLIST.length) + ' baris perlu diperbaiki.' : 'Tidak ada temuan. Data siap dipakai untuk laporan.') + '</p>';
  $('qgrid').innerHTML = Object.keys(MASALAH).map(function (x) { return '<div class="q ' + (cnt[x] ? '' : 'ok') + '"><b>' + (cnt[x] || 0) + '</b><span>' + MASALAH[x] + '</span></div>'; }).join('');
  var nb = $('nbKualitas'); nb.textContent = QLIST.length || '✓'; nb.className = 'nb' + (QLIST.length ? '' : ' ok');
  if (!$('fQ').options[1]) Object.keys(MASALAH).forEach(function (x) { $('fQ').add(new Option(MASALAH[x], x)); });
  tabelQ();
}
function qFiltered() { var f = $('fQ').value; return QLIST.filter(function (r) { return !f || r.m.indexOf(f) > -1; }); }
function tabelQ() {
  var d = qFiltered();
  $('qInfo').textContent = num(d.length) + ' baris bermasalah. Klik baris untuk melihat detail.';
  $('qBody').innerHTML = d.length ? d.map(function (r) {
    return '<tr data-i="' + DATA.groups.indexOf(r.g) + '"><td>' + esc(r.g.kecamatan) + '</td><td><b>' + esc(r.g.poktan) + '</b><small>No. ' + esc(r.g.no) + '</small></td><td>' + r.m.map(function (x) { return '<span class="mb">' + MASALAH[x] + '</span>'; }).join('') + '</td></tr>';
  }).join('') : '<tr><td colspan="3" class="kosong">Tidak ada masalah data pada kategori ini.</td></tr>';
}
function csv(nama, rows) {
  var head = ['Kecamatan','Nama Poktan','ID Poktan','Anggota','Desa/Kelurahan','Ketua','Alamat','Tahun dibantu'];
  var out = rows.map(function (g) {
    return [g.kecamatan, g.poktan, g.idPoktan, g.jumlahAnggota, g.desa, g.ketua, g.alamat, DATA.years.filter(function (y) { return adaTahun(g, y); }).join(' ')]
      .map(function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(',');
  });
  var a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob(['\ufeff' + head.join(',') + '\n' + out.join('\n')], {type:'text/csv;charset=utf-8;'}));
  a.download = nama + '.csv'; document.body.appendChild(a); a.click(); a.remove();
}
document.querySelectorAll('.tab-btn').forEach(function (b) {
  b.onclick = function () {
    document.querySelectorAll('.mon .tab-btn,.mon .panel').forEach(function (e) { e.classList.remove('aktif'); });
    b.classList.add('aktif'); $('panel-' + b.dataset.tab).classList.add('aktif');
  };
});
['fCari','fKec','fStatus','fTahun'].forEach(function (id) { $(id).addEventListener(id === 'fCari' ? 'input' : 'change', function () { P = 1; tabel(); }); });
$('fPrio').onchange = prioritas; $('fQ').onchange = tabelQ;
$('btnCsvQ').onclick = function () { csv('masalah-data', qFiltered().map(function (r) { return r.g; })); };
$('prev').onclick = function () { P--; tabel(); }; $('next').onclick = function () { P++; tabel(); };
$('btnCsvData').onclick = function () { csv('data-kelompok-tani', filtered()); };
$('btnCsvPrio').onclick = function () { csv('prioritas-bantuan', prioData()); };
$('btnXlsData').onclick = function () {
  var p = new URLSearchParams({q:$('fCari').value.trim(), kecamatan:$('fKec').value, status:$('fStatus').value, tahun:$('fTahun').value});
  location.href = DATA.exportUrl + '?' + p.toString();
};
document.addEventListener('click', function (e) {
  var r = e.target.closest('.mon tr[data-i],.mon .pr[data-i]');
  if (r) return detail(+r.dataset.i);
  if (e.target.id === 'drawer' || e.target.id === 'tutup') $('drawer').classList.remove('buka');
});
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') $('drawer').classList.remove('buka'); });
render();
})();
</script>
@endsection
