@extends('admin.layout')
@section('title', 'SIPANDAI — Tambah Pasien')
@section('content')
<style>.hw{background:#fff;border:1px solid #dbe5ff;border-radius:10px;padding:16px;margin-bottom:14px}.hw .skkh{background:#f4f7ff;border:1px dashed #A9C0FF;border-radius:8px;padding:12px;margin-bottom:12px}</style>
<div id="hasil" class="card card-success" hidden>
  <div class="card-header"><h3 class="card-title"><i class="fas fa-check-circle"></i> Pendaftaran berhasil</h3></div>
  <div class="card-body"><p class="mb-1 text-muted" id="h-ket">Nomor pendaftaran</p><h2 id="h-no" style="font-family:monospace;color:#1F4FBF">—</h2><ul id="h-hewan" class="mb-0 pl-3"></ul></div>
  <div class="card-footer"><button class="btn btn-primary" id="lagi"><i class="fas fa-plus"></i> Tambah pasien lagi</button> <a class="btn btn-default" href="{{ route('admin.sipandai.antrian.index') }}">Lihat antrian</a> <a class="btn btn-default" href="{{ route('admin.sipandai.pendaftaran.index') }}">Daftar pendaftaran</a></div>
</div>
<div id="gagal" class="alert alert-danger" hidden></div>
<form id="form" novalidate autocomplete="off">
  <div class="card"><div class="card-header"><h3 class="card-title">Data pemilik</h3></div>
    <div class="card-body"><div class="form-row">
      <div class="form-group col-md-6"><label for="nama_pemilik">Nama lengkap <span class="text-danger">*</span></label><input id="nama_pemilik" class="form-control"><div class="invalid-feedback" data-error-for="nama_pemilik"></div></div>
      <div class="form-group col-md-6"><label for="nik">NIK <span class="text-danger">*</span></label><input id="nik" class="form-control" inputmode="numeric" maxlength="16" placeholder="16 digit sesuai KTP"><div class="invalid-feedback" data-error-for="nik"></div></div>
      <div class="form-group col-md-6"><label for="no_hp">Nomor WhatsApp/HP <span class="text-danger">*</span></label><input id="no_hp" class="form-control" inputmode="tel" placeholder="Contoh: 08123456789"><div class="invalid-feedback" data-error-for="no_hp"></div></div>
      <div class="form-group col-md-6"><label for="alamat">Alamat <span class="text-danger">*</span></label><input id="alamat" class="form-control"><div class="invalid-feedback" data-error-for="alamat"></div></div>
    </div></div></div>
  <div class="card"><div class="card-header"><h3 class="card-title">Data hewan &amp; pelayanan</h3><div class="card-tools"><span id="jml" class="badge badge-primary">1 hewan</span></div></div>
    <div class="card-body"><div id="list"></div><button type="button" id="tambahHewan" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Tambah hewan lain</button></div></div>
  <button class="btn btn-primary btn-lg" id="kirim"><span class="lbl">Simpan pendaftaran</span></button>
</form>

<template id="tpl">
  <div class="hw" data-idx="__IDX__">
    <div class="d-flex justify-content-between mb-2"><b class="no">Hewan 1</b><button type="button" class="btn btn-xs btn-outline-danger del"><i class="fas fa-trash"></i> Hapus</button></div>
    <div class="form-row">
      <div class="form-group col-md-4"><label for="nama_hewan___IDX__">Nama hewan <span class="text-danger">*</span></label><input id="nama_hewan___IDX__" class="form-control"><div class="invalid-feedback" data-error-for="nama_hewan___IDX__"></div></div>
      <div class="form-group col-md-4"><label for="jenis_hewan___IDX__">Jenis hewan <span class="text-danger">*</span></label><select id="jenis_hewan___IDX__" class="form-control"><option value="">Pilih jenis hewan</option>@foreach($cfg['hewan'] as $x)<option value="{{ $x }}">{{ $x }}</option>@endforeach</select><div class="invalid-feedback" data-error-for="jenis_hewan___IDX__"></div></div>
      <div class="form-group col-md-4"><label for="ras___IDX__">Ras</label><input id="ras___IDX__" class="form-control"></div>
      <div class="form-group col-md-4"><label>Jenis kelamin <span class="text-danger">*</span></label><div><label class="mr-3 mb-0"><input type="radio" name="jk___IDX__" value="Jantan"> Jantan</label><label class="mb-0"><input type="radio" name="jk___IDX__" value="Betina"> Betina</label></div><div class="invalid-feedback" data-error-for="jenis_kelamin___IDX__"></div></div>
      <div class="form-group col-md-4"><label for="umur_tgl_lahir___IDX__">Umur atau tanggal lahir <span class="text-danger">*</span></label><input id="umur_tgl_lahir___IDX__" class="form-control" placeholder="Contoh: 2 tahun / 03-2024"><div class="invalid-feedback" data-error-for="umur_tgl_lahir___IDX__"></div></div>
      <div class="form-group col-md-4"><label for="ciri_khusus___IDX__">Warna / ciri khusus</label><input id="ciri_khusus___IDX__" class="form-control"></div>
      <div class="form-group col-md-12"><label for="jenis_pelayanan___IDX__">Jenis pelayanan <span class="text-danger">*</span></label><select id="jenis_pelayanan___IDX__" class="form-control"><option value="">Pilih jenis pelayanan</option>@foreach($cfg['pelayanan'] as $x)<option value="{{ $x }}">{{ $x }}</option>@endforeach</select><div class="invalid-feedback" data-error-for="jenis_pelayanan___IDX__"></div></div>
    </div>
    <div class="skkh" data-skkh="__IDX__" hidden>
      <b class="d-block mb-2 text-primary">Data verifikasi &amp; penerbitan SKKH</b>
      <div class="form-row">
        <div class="form-group col-md-6"><label for="skkh_tanggal___IDX__">Hari/tanggal <span class="text-danger">*</span></label><input type="date" id="skkh_tanggal___IDX__" class="form-control"><div class="invalid-feedback" data-error-for="skkh_tanggal___IDX__"></div></div>
        <div class="form-group col-md-6"><label for="skkh_nama_penerima___IDX__">Nama penerima <span class="text-danger">*</span></label><input id="skkh_nama_penerima___IDX__" class="form-control"><div class="invalid-feedback" data-error-for="skkh_nama_penerima___IDX__"></div></div>
        <div class="form-group col-md-12"><label for="skkh_alamat_penerima___IDX__">Alamat penerima <span class="text-danger">*</span></label><input id="skkh_alamat_penerima___IDX__" class="form-control"><div class="invalid-feedback" data-error-for="skkh_alamat_penerima___IDX__"></div></div>
        <div class="form-group col-md-6"><label for="skkh_jumlah_jantan___IDX__">Jumlah hewan jantan</label><input type="number" min="0" id="skkh_jumlah_jantan___IDX__" class="form-control"></div>
        <div class="form-group col-md-6"><label for="skkh_jumlah_betina___IDX__">Jumlah hewan betina</label><input type="number" min="0" id="skkh_jumlah_betina___IDX__" class="form-control"></div>
      </div><div class="invalid-feedback" data-error-for="skkh_jumlah___IDX__"></div>
    </div>
    <div class="form-group"><label for="keluhan_utama___IDX__">Keluhan utama <span class="text-danger">*</span></label><textarea id="keluhan_utama___IDX__" rows="2" class="form-control"></textarea><div class="invalid-feedback" data-error-for="keluhan_utama___IDX__"></div></div>
    <div class="form-group mb-0"><label for="info_tambahan___IDX__">Informasi tambahan</label><textarea id="info_tambahan___IDX__" rows="2" class="form-control"></textarea></div>
  </div>
</template>

<script>
(function () {
  var SKKH = @json($cfg['skkh']), URL_SIMPAN = @json(route('admin.sipandai.pendaftaran.simpan')), TOKEN = @json(csrf_token());
  var $i = function (id) { return document.getElementById(id); };
  var form = $i('form'), list = $i('list'), tpl = $i('tpl').innerHTML, n = 0, busy = false;
  function val(id) { var e = $i(id); return e ? e.value : ''; }
  function jk(i) { var c = form.querySelector('input[name="jk_' + i + '"]:checked'); return c ? c.value : ''; }
  function err(id, msg) {
    var e = form.querySelector('[data-error-for="' + id + '"]'), f = $i(id);
    if (e) { e.textContent = msg || ''; e.style.display = msg ? 'block' : 'none'; }
    if (f) f.classList.toggle('is-invalid', !!msg);
  }
  var VP = {
    nama_pemilik: function (v) { return v.trim() ? null : T('Nama lengkap wajib diisi.'); },
    nik: function (v) { return !v.trim() ? T('NIK wajib diisi.') : (!/^\d{16}$/.test(v.trim()) ? T('NIK harus terdiri dari 16 digit angka.') : null); },
    alamat: function (v) { return v.trim() ? null : T('Alamat wajib diisi.'); },
    no_hp: function (v) { var c = v.trim().replace(/[\s-]/g, ''); return !c ? T('Nomor HP/WhatsApp wajib diisi.') : (!/^(\+62|62|0)8\d{7,12}$/.test(c) ? T('Format nomor HP tidak valid.') : null); }
  };
  var VH = {
    nama_hewan: function (v) { return v.trim() ? null : T('Nama hewan wajib diisi.'); },
    jenis_hewan: function (v) { return v ? null : T('Jenis hewan wajib dipilih.'); },
    umur_tgl_lahir: function (v) { return v.trim() ? null : T('Umur atau tanggal lahir wajib diisi.'); },
    jenis_pelayanan: function (v) { return v ? null : T('Jenis pelayanan wajib dipilih.'); },
    keluhan_utama: function (v) { return v.trim() ? null : T('Keluhan utama wajib diisi.'); }
  };
  var VS = {
    skkh_tanggal: function (v) { return v.trim() ? null : T('Hari/tanggal wajib diisi.'); },
    skkh_nama_penerima: function (v) { return v.trim() ? null : T('Nama penerima wajib diisi.'); },
    skkh_alamat_penerima: function (v) { return v.trim() ? null : T('Alamat penerima wajib diisi.'); }
  };
  function idx() { return [].map.call(list.querySelectorAll('.hw'), function (c) { return c.getAttribute('data-idx'); }); }
  function skkhOn(i) { return val('jenis_pelayanan_' + i) === SKKH; }
  function cek(i) {
    var ok = true;
    Object.keys(VH).forEach(function (f) { var e = VH[f](val(f + '_' + i)); err(f + '_' + i, e); if (e) ok = false; });
    var e2 = jk(i) ? null : T('Jenis kelamin hewan wajib dipilih.'); err('jenis_kelamin_' + i, e2); if (e2) ok = false;
    if (skkhOn(i)) {
      Object.keys(VS).forEach(function (f) { var e = VS[f](val(f + '_' + i)); err(f + '_' + i, e); if (e) ok = false; });
      var j = parseInt(val('skkh_jumlah_jantan_' + i), 10) || 0, b = parseInt(val('skkh_jumlah_betina_' + i), 10) || 0;
      var e3 = (j + b <= 0) ? T('Isi jumlah jantan dan/atau betina, minimal 1 ekor.') : null; err('skkh_jumlah_' + i, e3); if (e3) ok = false;
    }
    return ok;
  }
  function renum() {
    var cs = list.querySelectorAll('.hw');
    cs.forEach(function (c, k) { c.querySelector('.no').textContent = T('Hewan') + ' ' + (k + 1); c.querySelector('.del').hidden = cs.length < 2; });
    $i('jml').textContent = cs.length + ' ' + T('hewan');
  }
  function tambah() {
    var i = ++n, w = document.createElement('div'); w.innerHTML = tpl.split('__IDX__').join(i).trim();
    var c = w.firstElementChild; list.appendChild(c);
    c.querySelector('#jenis_pelayanan_' + i).onchange = function () { c.querySelector('[data-skkh="' + i + '"]').hidden = !skkhOn(i); };
    c.querySelector('.del').onclick = function () { c.remove(); renum(); };
    renum();
  }
  $i('tambahHewan').onclick = tambah; tambah();

  function reset() { form.reset(); list.innerHTML = ''; n = 0; tambah(); $i('hasil').hidden = true; form.hidden = false; scrollTo(0, 0); }
  $i('lagi').onclick = reset;
  form.addEventListener('submit', function (e) {
    e.preventDefault(); if (busy) return;
    var ok = true;
    Object.keys(VP).forEach(function (k) { var m = VP[k](val(k)); err(k, m); if (m) ok = false; });
    idx().forEach(function (i) { if (!cek(i)) ok = false; });
    if (!ok) { var f = form.querySelector('.is-invalid'); if (f) f.scrollIntoView({block: 'center'}); return; }
    var body = {pemilik: {nama_pemilik: val('nama_pemilik').trim(), nik: val('nik').trim(), alamat: val('alamat').trim(), no_hp: val('no_hp').trim()},
      hewan: idx().map(function (i) { return {nama_hewan: val('nama_hewan_' + i).trim(), jenis_hewan: val('jenis_hewan_' + i), ras: val('ras_' + i).trim(), jenis_kelamin: jk(i),
        umur_tgl_lahir: val('umur_tgl_lahir_' + i).trim(), ciri_khusus: val('ciri_khusus_' + i).trim(), jenis_pelayanan: val('jenis_pelayanan_' + i),
        keluhan_utama: val('keluhan_utama_' + i).trim(), info_tambahan: val('info_tambahan_' + i).trim(), skkh_tanggal: val('skkh_tanggal_' + i),
        skkh_nama_penerima: val('skkh_nama_penerima_' + i).trim(), skkh_alamat_penerima: val('skkh_alamat_penerima_' + i).trim(),
        skkh_jumlah_jantan: val('skkh_jumlah_jantan_' + i), skkh_jumlah_betina: val('skkh_jumlah_betina_' + i)}; })};
    busy = true; $i('kirim').disabled = true; $i('kirim').querySelector('.lbl').textContent = T('Mengirim…'); $i('gagal').hidden = true;
    fetch(URL_SIMPAN, {method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN}, body: JSON.stringify(body)})
      .then(function (r) { return r.json().catch(function () { return {message: r.status === 419 ? T('Sesi habis. Muat ulang halaman lalu kirim lagi.') : null}; }); })
      .then(function (d) {
        busy = false; $i('kirim').disabled = false; $i('kirim').querySelector('.lbl').textContent = T('Simpan pendaftaran');
        if (d && d.success) {
          $i('h-no').textContent = d.noPendaftaran; $i('h-ket').textContent = d.jumlahHewan + ' ' + T('ekor hewan didaftarkan dengan nomor');
          var ul = $i('h-hewan'); ul.innerHTML = '';
          d.hewan.forEach(function (h) { var li = document.createElement('li'); li.textContent = h.nama + ' (' + h.jenis + ') — ' + h.pelayanan; ul.appendChild(li); });
          form.hidden = true; $i('hasil').hidden = false; scrollTo(0, 0);
        } else { $i('gagal').hidden = false; $i('gagal').textContent = (d && d.message) || T('Terjadi kendala saat menyimpan data. Periksa koneksi internet lalu coba lagi.'); scrollTo(0, 0); }
      }).catch(function () { busy = false; $i('kirim').disabled = false; $i('gagal').hidden = false; $i('gagal').textContent = T('Terjadi kendala saat menyimpan data. Periksa koneksi internet lalu coba lagi.'); });
  });
})();
</script>
@endsection
