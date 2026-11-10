@extends('admin.layout')
@section('title', 'Kelompok Tani (SIMLUHTAN)')
@section('content')
@unless($adaLib)<div class="alert alert-danger">Library Excel belum terpasang. Jalankan di server: <code>composer require phpoffice/phpspreadsheet</code></div>@endunless
@foreach(session('hasil', []) as $h)
  <div class="alert alert-{{ $h['ok'] ? 'success' : 'danger' }}">
    <b>{{ $h['judul'] }}</b> — {{ $h['ringkas'] }}
    @if($h['err'])<ul class="mb-0 mt-2 small">@foreach(array_slice($h['err'], 0, 30) as $e)<li>{{ $e }}</li>@endforeach @if(count($h['err']) > 30)<li>… dan {{ count($h['err']) - 30 }} catatan lainnya</li>@endif</ul>@endif
  </div>
@endforeach
<div class="row">
  <div class="col-md-6"><div class="card card-primary"><div class="card-header"><h3 class="card-title">Langkah 1 — Impor List Kelompok Tani</h3></div>
    <form method="post" enctype="multipart/form-data" action="{{ route('admin.kelompok.impor.poktan') }}">@csrf
      <div class="card-body">
        <p class="small text-muted">Kolom: No, Nama Poktan, ID Poktan, Jumlah Anggota, Nama Desa, Nama Ketua, Alamat Sekretariat. Data dengan ID Poktan yang sama akan diperbarui, bukan digandakan.</p>
        <input type="file" name="file" accept=".xlsx,.xls,.csv" required>@error('file')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-file-import"></i> Impor</button> <a href="{{ route('admin.kelompok.template', 'poktan') }}" class="btn btn-link">Unduh template</a></div>
    </form></div></div>
  <div class="col-md-6"><div class="card card-success" id="impAng"><div class="card-header"><h3 class="card-title">Langkah 2 — Impor Daftar Anggota (banyak file)</h3></div>
    <div class="card-body">
      @csrf
      <p class="small text-muted">Pilih puluhan file sekaligus (Ctrl/Shift + klik) atau satu folder penuh, lalu klik <b>Mulai impor</b>. File diunggah satu per satu otomatis, jadi tidak terkena batas jumlah file maupun waktu PHP. Kelompok dikenali dari baris <b>ID Kelompok Tani</b>, jadi lakukan Langkah 1 lebih dulu.</p>
      <div id="drop" style="border:2px dashed #9db8a8;border-radius:10px;padding:16px;text-align:center">
        Tarik file ke sini, atau
        <label class="btn btn-sm btn-outline-success mb-0 mx-1">Pilih file<input type="file" id="fFiles" multiple accept=".xlsx,.xls,.csv" hidden></label>
        <label class="btn btn-sm btn-outline-secondary mb-0">Pilih folder<input type="file" id="fDir" webkitdirectory hidden></label>
      </div>
      <div class="form-check mt-2"><input type="checkbox" class="form-check-input" id="sk2"><label for="sk2" class="form-check-label small">Sinkronkan: hapus anggota kelompok yang tidak ada di file</label></div>
      <div class="progress mt-3" style="height:20px"><div class="progress-bar bg-success" id="pbar" style="width:0;min-width:2.5rem">0%</div></div>
      <div id="antri" class="small mt-2" style="max-height:280px;overflow:auto"></div>
    </div>
    <div class="card-footer"><button class="btn btn-success" id="btnMulai" disabled><i class="fas fa-file-import"></i> Mulai impor</button> <button class="btn btn-default" id="btnBatal" disabled>Hentikan</button> <a href="{{ route('admin.kelompok.template', 'anggota') }}" class="btn btn-link">Unduh template</a> <span id="ringk" class="small ml-1"></span></div>
  </div></div>
<script>
(function () {
  var q = [], stop = false, sudah = 0, ok = 0, gagal = 0, total = 0;
  var $i = function (id) { return document.getElementById(id); };
  var token = document.querySelector('#impAng input[name=_token]').value, url = @json(route('admin.kelompok.impor.anggota'));
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  function ring() {
    var p = total ? Math.round(sudah / total * 100) : 0;
    $i('pbar').style.width = p + '%'; $i('pbar').textContent = p + '% (' + sudah + '/' + total + ')';
    $i('ringk').textContent = ok + ' berhasil, ' + gagal + ' gagal';
  }
  function tambah(list) {
    Array.prototype.forEach.call(list, function (f) {
      if (!/\.(xlsx|xls|csv)$/i.test(f.name) || f.name.indexOf('~$') === 0) return;
      var row = document.createElement('div');
      row.innerHTML = '<span class="badge badge-secondary">menunggu</span> ' + esc(f.name) + '<span class="msg"></span>';
      $i('antri').appendChild(row); q.push({f: f, row: row}); total++;
    });
    $i('btnMulai').disabled = !q.length; ring();
  }
  function set(it, cls, txt, msg, err) {
    var b = it.row.querySelector('.badge'); b.className = 'badge badge-' + cls; b.textContent = txt;
    it.row.querySelector('.msg').textContent = msg ? ' — ' + msg : '';
    if (err && err.length) {
      var u = document.createElement('ul'); u.className = 'mb-1';
      err.slice(0, 5).forEach(function (e) { var l = document.createElement('li'); l.textContent = e; u.appendChild(l); });
      if (err.length > 5) { var l = document.createElement('li'); l.textContent = '… +' + (err.length - 5) + ' catatan lainnya'; u.appendChild(l); }
      it.row.appendChild(u);
    }
  }
  async function jalan() {
    stop = false; $i('btnMulai').disabled = true; $i('btnBatal').disabled = false;
    while (q.length && !stop) {
      var it = q.shift(); set(it, 'info', 'mengunggah');
      var fd = new FormData(); fd.append('_token', token); fd.append('files[]', it.f); if ($i('sk2').checked) fd.append('sinkron', '1');
      try {
        var r = await fetch(url, {method: 'POST', body: fd, headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}});
        var j = await r.json().catch(function () { return null; });
        if (r.ok && j && j.hasil && j.hasil[0]) { var h = j.hasil[0]; set(it, h.ok ? 'success' : 'danger', h.ok ? 'berhasil' : 'gagal', h.ringkas, h.err); h.ok ? ok++ : gagal++; }
        else { var m = (j && (j.message || '')) || ('HTTP ' + r.status); set(it, 'danger', 'gagal', m); gagal++; }
      } catch (e) { set(it, 'danger', 'gagal', 'Koneksi terputus: ' + e.message); gagal++; }
      sudah++; ring();
    }
    $i('btnBatal').disabled = true; $i('btnMulai').disabled = !q.length;
  }
  $i('fFiles').onchange = function () { tambah(this.files); this.value = ''; };
  $i('fDir').onchange = function () { tambah(this.files); this.value = ''; };
  $i('btnMulai').onclick = jalan; $i('btnBatal').onclick = function () { stop = true; };
  var d = $i('drop');
  ['dragover', 'dragenter'].forEach(function (ev) { d.addEventListener(ev, function (e) { e.preventDefault(); d.style.background = '#eef6f1'; }); });
  ['dragleave', 'drop'].forEach(function (ev) { d.addEventListener(ev, function (e) { e.preventDefault(); d.style.background = ''; }); });
  d.addEventListener('drop', function (e) { tambah(e.dataTransfer.files); });
})();
</script>
</div>
<div class="row"><div class="col-12"><div class="card card-warning"><div class="card-header"><h3 class="card-title">Langkah 3 — Impor Riwayat Bantuan (2020 – sekarang)</h3></div>
  <form method="post" enctype="multipart/form-data" action="{{ route('admin.kelompok.impor.bantuan') }}">@csrf
    <div class="card-body">
      <p class="small text-muted mb-2">Format tabel monitoring: No., Nama Kecamatan, Nama Poktan, ID Poktan, Jumlah Anggota, Nama Desa, Nama Ketua, Alamat, lalu kolom per tahun (APBD Kota, APBD Provinsi, APBN). Sel berisi keterangan bantuan = ada bantuan; sel kosong, "-", atau "0" = tidak ada. Tahun baru (mis. 2027) cukup ditambahkan sebagai kolom baru. Kelompok dicocokkan lewat ID Poktan (jika kosong: nama + kecamatan); kelompok yang belum ada otomatis dibuat.</p>
      <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
      <div class="form-check mt-2"><input type="checkbox" name="sinkron" value="1" class="form-check-input" id="sk3"><label for="sk3" class="form-check-label small">Sinkronkan: hapus bantuan di tahun-tahun yang ada di file bila selnya kosong</label></div>
    </div>
    <div class="card-footer"><button class="btn btn-warning"><i class="fas fa-file-import"></i> Impor</button> <a href="{{ route('admin.kelompok.template', 'bantuan') }}" class="btn btn-link">Unduh template</a></div>
  </form></div></div></div>
<div class="card">
  <div class="card-header"><form class="form-inline" method="get"><input name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2" placeholder="Cari nama / ID / desa / ketua"><button class="btn btn-sm btn-secondary">Cari</button></form> <a href="{{ route('admin.kelompok.create') }}" class="btn btn-sm btn-primary float-right"><i class="fas fa-plus"></i> Tambah kelompok</a></div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped table-sm">
      <thead><tr><th>ID Poktan</th><th>Nama Poktan</th><th>Desa</th><th>Ketua</th><th class="text-right">Anggota (SIMLUH)</th><th class="text-right">Terdata</th><th style="width:60px"></th></tr></thead>
      @forelse($rows as $k)
        <tr><td>{{ $k->id_poktan }}</td><td><b>{{ $k->nama }}</b></td><td>{{ $k->desa }}</td><td>{{ $k->ketua }}</td><td class="text-right">{{ $k->jumlah_anggota }}</td>
          <td class="text-right"><span class="badge badge-{{ $k->anggotas_count == $k->jumlah_anggota ? 'success' : ($k->anggotas_count ? 'warning' : 'secondary') }}">{{ $k->anggotas_count }}</span></td>
          <td><a class="btn btn-xs btn-info" href="{{ route('admin.kelompok.show', $k) }}"><i class="fas fa-eye"></i></a></td></tr>
      @empty<tr><td colspan="7" class="text-center text-muted">Belum ada data. Mulai dengan Langkah 1.</td></tr>@endforelse
    </table>
  </div>
  <div class="card-footer">{{ $rows->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
