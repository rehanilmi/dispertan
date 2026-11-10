@extends('layouts.app')
@section('title', 'SIPANDAI — Pendaftaran Pasien Hewan · Puskeswan Kota Padang')
@section('desc', 'Daftarkan hewan Anda ke Puskeswan Kota Padang secara online. Mendukung lebih dari satu hewan dan penerbitan SKKH.')
@section('content')
@php
$in = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm outline-none transition placeholder:text-muted-foreground focus-visible:border-padi focus-visible:ring-2 focus-visible:ring-padi/30 aria-[invalid=true]:border-red-500 aria-[invalid=true]:ring-2 aria-[invalid=true]:ring-red-500/20';
$ta = str_replace('h-10 ', 'min-h-[72px] ', $in);
$lbl = 'mb-1.5 block text-sm font-medium leading-none';
$err = 'mt-1.5 hidden text-xs font-medium text-red-600 data-[show]:block';
$card = 'rounded-xl border border-border bg-card text-card-foreground shadow-sm';
$btn = 'inline-flex h-10 items-center justify-center gap-2 whitespace-nowrap rounded-md px-4 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-padi/50 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-60';
$btnP = $btn.' bg-primary text-primary-foreground shadow hover:bg-primary/90';
$btnO = $btn.' border border-input bg-background hover:bg-muted';
@endphp
<div class="wrap max-w-3xl pb-20 pt-10">
  <header class="mb-8 flex items-start gap-4" data-anim>
    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-padi text-white shadow-lg shadow-padi/25"><i data-lucide="paw-print" class="h-7 w-7"></i></span>
    <div>
      <h1 class="font-display text-4xl font-bold tracking-tight md:text-5xl">SIPANDAI</h1>
      <p class="mt-1 text-muted-foreground">Sistem Pendaftaran Digital Terintegrasi · {{ $cfg['lokasi'] }}</p>
    </div>
  </header>

  {{-- Stepper --}}
  <ol class="mb-8 flex items-center gap-2 text-sm" aria-label="Tahapan pendaftaran" id="stepper">
    @foreach ([['Data pemilik', 'user-round'], ['Data hewan', 'paw-print'], ['Struk', 'receipt']] as $i => [$t, $ic])
      <li class="flex items-center gap-2" data-step="{{ $i + 1 }}" @if($i === 0) data-on @endif>
        <span class="step-dot grid h-8 w-8 place-items-center rounded-full border border-border bg-card text-muted-foreground transition"><i data-lucide="{{ $ic }}" class="h-4 w-4"></i></span>
        <span class="hidden font-medium text-muted-foreground transition sm:inline">{{ $t }}</span>
      </li>
      @if($i < 2)<li class="h-px flex-1 bg-border" aria-hidden="true"></li>@endif
    @endforeach
  </ol>

  <div id="view-intro" class="mb-6 flex gap-3 rounded-lg border border-border bg-muted/50 p-4 text-sm text-muted-foreground" data-anim>
    <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0 text-padi"></i>
    <p>Isi data pemilik sekali, lalu tambahkan lebih dari satu hewan bila perlu. Seluruh data tersimpan otomatis dan Anda akan menerima nomor pendaftaran beserta struk.</p>
  </div>

  <div id="view-error" hidden class="mb-6 flex gap-3 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-700 dark:text-red-300" role="alert">
    <i data-lucide="circle-alert" class="mt-0.5 h-4 w-4 shrink-0"></i><div><b class="block">Pendaftaran gagal dikirim</b><span id="error-desc"></span></div>
  </div>

  <form id="form-pendaftaran" novalidate class="space-y-6" autocomplete="on">
    <input type="text" name="website" id="hp" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

    <section class="{{ $card }}" data-anim>
      <div class="border-b border-border p-5"><h2 class="font-display text-lg font-semibold">Data pemilik</h2><p class="text-sm text-muted-foreground">Isi sesuai identitas KTP Anda.</p></div>
      <div class="grid gap-4 p-5 sm:grid-cols-2">
        <div class="sm:col-span-2"><label for="nama_pemilik" class="{{ $lbl }}">Nama lengkap <span class="text-red-500">*</span></label><input id="nama_pemilik" class="{{ $in }}" autocomplete="name" placeholder="Contoh: Siti Aminah"><p class="{{ $err }}" data-error-for="nama_pemilik"></p></div>
        <div><label for="nik" class="{{ $lbl }}">NIK <span class="text-red-500">*</span></label><input id="nik" class="{{ $in }}" inputmode="numeric" maxlength="16" placeholder="16 digit sesuai KTP"><p class="{{ $err }}" data-error-for="nik"></p></div>
        <div><label for="no_hp" class="{{ $lbl }}">Nomor WhatsApp/HP <span class="text-red-500">*</span></label><input id="no_hp" type="tel" class="{{ $in }}" inputmode="tel" placeholder="Contoh: 08123456789"><p class="{{ $err }}" data-error-for="no_hp"></p></div>
        <div class="sm:col-span-2"><label for="alamat" class="{{ $lbl }}">Alamat <span class="text-red-500">*</span></label><textarea id="alamat" class="{{ $ta }}" rows="2" placeholder="Alamat tempat tinggal saat ini"></textarea><p class="{{ $err }}" data-error-for="alamat"></p></div>
      </div>
    </section>

    <section class="{{ $card }}" data-anim>
      <div class="flex items-center justify-between gap-3 border-b border-border p-5">
        <div><h2 class="font-display text-lg font-semibold">Data hewan &amp; pelayanan</h2><p class="text-sm text-muted-foreground">Tambahkan hewan jika ingin mendaftarkan lebih dari satu.</p></div>
        <span id="badge-jumlah" class="rounded-full border border-border bg-muted px-2.5 py-0.5 text-xs font-semibold">1 hewan</span>
      </div>
      <div id="hewan-list" class="space-y-4 p-5"></div>
      <div class="border-t border-border p-5"><button type="button" id="btn-tambah-hewan" class="{{ $btnO }} w-full sm:w-auto"><i data-lucide="plus" class="h-4 w-4"></i> Tambah hewan lain</button></div>
    </section>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" data-anim>
      <p class="flex items-start gap-2 text-sm text-muted-foreground"><i data-lucide="shield-check" class="mt-0.5 h-4 w-4 shrink-0 text-padi"></i> Periksa kembali data sebelum mengirim. Data tidak dapat diubah setelah terkirim.</p>
      <button type="submit" id="btn-submit" class="{{ $btnP }} h-11 px-6"><i data-lucide="loader-circle" class="spin hidden h-4 w-4 animate-spin"></i><span class="lbl">Kirim pendaftaran</span></button>
    </div>
  </form>

  {{-- Struk / kartu konfirmasi --}}
  <section id="view-struk" hidden aria-live="polite">
    <div id="struk" class="mx-auto max-w-md overflow-hidden rounded-2xl border border-border bg-card shadow-xl">
      <div class="bg-padi px-6 py-6 text-white">
        <div class="flex items-center gap-3"><span id="struk-check" class="grid h-12 w-12 place-items-center rounded-full bg-white/20"><i data-lucide="circle-check-big" class="h-7 w-7"></i></span>
          <div><h2 class="font-display text-xl font-bold leading-tight">Pendaftaran berhasil</h2><p class="text-xs text-white/80" id="s-lokasi"></p></div></div>
      </div>
      <div class="px-6 pb-5 pt-6">
        <p class="text-xs uppercase tracking-wide text-muted-foreground" id="s-ket">Nomor pendaftaran Anda</p>
        <p id="s-no" class="mt-1 break-all font-mono text-2xl font-bold tracking-tight text-padi dark:text-blue-300">—</p>
      </div>
      <div class="relative mx-0 border-t-2 border-dashed border-border"><span class="absolute -left-3 -top-3 h-6 w-6 rounded-full bg-background"></span><span class="absolute -right-3 -top-3 h-6 w-6 rounded-full bg-background"></span></div>
      <dl class="grid grid-cols-2 gap-x-4 gap-y-3 px-6 py-5 text-sm">
        <div><dt class="text-xs text-muted-foreground">Tanggal</dt><dd class="font-medium" id="s-tgl"></dd></div>
        <div><dt class="text-xs text-muted-foreground">Waktu</dt><dd class="font-medium" id="s-waktu"></dd></div>
        <div class="col-span-2"><dt class="text-xs text-muted-foreground">Pemilik</dt><dd class="font-medium"><span id="s-pemilik"></span> <span class="font-mono text-xs text-muted-foreground" id="s-nik"></span></dd></div>
        <div class="col-span-2"><dt class="mb-1.5 text-xs text-muted-foreground">Hewan terdaftar</dt><dd><ul id="s-hewan" class="space-y-2"></ul></dd></div>
      </dl>
      <p class="border-t border-border bg-muted/50 px-6 py-4 text-center text-xs text-muted-foreground">Tunjukkan nomor ini kepada petugas saat dipanggil. Simpan atau cetak struk ini.</p>
    </div>
    <div class="mx-auto mt-5 flex max-w-md flex-wrap justify-center gap-3 no-print">
      <button type="button" id="btn-cetak" class="{{ $btnP }}"><i data-lucide="printer" class="h-4 w-4"></i> Cetak struk</button>
      <button type="button" id="btn-salin" class="{{ $btnO }}"><i data-lucide="copy" class="h-4 w-4"></i> <span>Salin nomor</span></button>
      <button type="button" id="btn-daftar-lagi" class="{{ $btnO }}"><i data-lucide="rotate-ccw" class="h-4 w-4"></i> Daftar lagi</button>
    </div>
  </section>
</div>

{{-- Template 1 kartu hewan --}}
<template id="tpl-hewan">
  <div class="hewan-card rounded-lg border border-border bg-background/60 p-4" data-hewan-index="__IDX__">
    <div class="mb-4 flex items-center justify-between">
      <span class="hewan-no inline-flex items-center gap-2 text-sm font-semibold"><i data-lucide="paw-print" class="h-4 w-4 text-padi"></i><span class="lbl">Hewan 1</span></span>
      <button type="button" class="btn-hapus inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-500/10" aria-label="Hapus hewan ini"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus</button>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
      <div><label for="nama_hewan___IDX__" class="{{ $lbl }}">Nama hewan <span class="text-red-500">*</span></label><input id="nama_hewan___IDX__" data-field="nama_hewan" class="{{ $in }}" placeholder="Contoh: Kitty"><p class="{{ $err }}" data-error-for="nama_hewan___IDX__"></p></div>
      <div><label for="jenis_hewan___IDX__" class="{{ $lbl }}">Jenis hewan <span class="text-red-500">*</span></label>
        <select id="jenis_hewan___IDX__" data-field="jenis_hewan" class="{{ $in }}"><option value="" disabled selected>Pilih jenis hewan</option>@foreach($cfg['hewan'] as $x)<option value="{{ $x }}">{{ $x }}</option>@endforeach</select><p class="{{ $err }}" data-error-for="jenis_hewan___IDX__"></p></div>
      <div><label for="ras___IDX__" class="{{ $lbl }}">Ras</label><input id="ras___IDX__" data-field="ras" class="{{ $in }}" placeholder="Persia, Kampung, dll (jika diketahui)"></div>
      <div><span class="{{ $lbl }}">Jenis kelamin <span class="text-red-500">*</span></span>
        <div class="flex gap-2" role="radiogroup" aria-label="Jenis kelamin hewan">
          @foreach (['Jantan' => 'mars', 'Betina' => 'venus'] as $jk => $ic)
            <label class="cursor-pointer"><input type="radio" name="jenis_kelamin___IDX__" value="{{ $jk }}" data-field="jenis_kelamin" class="peer sr-only"><span class="inline-flex h-10 items-center gap-1.5 rounded-md border border-input bg-background px-4 text-sm transition hover:bg-muted peer-checked:border-padi peer-checked:bg-padi/10 peer-checked:font-semibold peer-checked:text-padi peer-focus-visible:ring-2 peer-focus-visible:ring-padi/40 dark:peer-checked:text-blue-300"><i data-lucide="{{ $ic }}" class="h-4 w-4"></i>{{ $jk }}</span></label>
          @endforeach
        </div><p class="{{ $err }}" data-error-for="jenis_kelamin___IDX__"></p></div>
      <div><label for="umur_tgl_lahir___IDX__" class="{{ $lbl }}">Umur atau tanggal lahir <span class="text-red-500">*</span></label><input id="umur_tgl_lahir___IDX__" data-field="umur_tgl_lahir" class="{{ $in }}" placeholder="Contoh: 2 tahun / 03-2024"><p class="{{ $err }}" data-error-for="umur_tgl_lahir___IDX__"></p></div>
      <div><label for="ciri_khusus___IDX__" class="{{ $lbl }}">Warna / ciri khusus <span class="font-normal text-muted-foreground">(opsional)</span></label><input id="ciri_khusus___IDX__" class="{{ $in }}" placeholder="Contoh: bulu putih belang oranye"></div>
      <div class="sm:col-span-2"><label for="jenis_pelayanan___IDX__" class="{{ $lbl }}">Jenis pelayanan <span class="text-red-500">*</span></label>
        <select id="jenis_pelayanan___IDX__" data-field="jenis_pelayanan" class="{{ $in }}"><option value="" disabled selected>Pilih jenis pelayanan</option>@foreach($cfg['pelayanan'] as $x)<option value="{{ $x }}">{{ $x }}</option>@endforeach</select><p class="{{ $err }}" data-error-for="jenis_pelayanan___IDX__"></p></div>

      <div class="skkh-block sm:col-span-2 space-y-4 rounded-lg border border-dashed border-padi/40 bg-padi/5 p-4" data-skkh-block="__IDX__" hidden>
        <p class="flex items-center gap-2 text-sm font-semibold text-padi dark:text-blue-300"><i data-lucide="file-badge" class="h-4 w-4"></i> Data verifikasi &amp; penerbitan SKKH</p>
        <div class="grid gap-4 sm:grid-cols-2">
          <div><label for="skkh_tanggal___IDX__" class="{{ $lbl }}">Hari/tanggal <span class="text-red-500">*</span></label><input type="date" id="skkh_tanggal___IDX__" class="{{ $in }}"><p class="{{ $err }}" data-error-for="skkh_tanggal___IDX__"></p></div>
          <div><label for="skkh_nama_penerima___IDX__" class="{{ $lbl }}">Nama penerima <span class="text-red-500">*</span></label><input id="skkh_nama_penerima___IDX__" class="{{ $in }}" placeholder="Penerima hewan di tempat tujuan"><p class="{{ $err }}" data-error-for="skkh_nama_penerima___IDX__"></p></div>
          <div class="sm:col-span-2"><label for="skkh_alamat_penerima___IDX__" class="{{ $lbl }}">Alamat penerima <span class="text-red-500">*</span></label><textarea id="skkh_alamat_penerima___IDX__" class="{{ $ta }}" rows="2" placeholder="Alamat tujuan pengiriman hewan"></textarea><p class="{{ $err }}" data-error-for="skkh_alamat_penerima___IDX__"></p></div>
          <div><label for="skkh_jumlah_jantan___IDX__" class="{{ $lbl }}">Jumlah hewan jantan</label><input type="number" min="0" step="1" id="skkh_jumlah_jantan___IDX__" class="{{ $in }}" placeholder="0"></div>
          <div><label for="skkh_jumlah_betina___IDX__" class="{{ $lbl }}">Jumlah hewan betina</label><input type="number" min="0" step="1" id="skkh_jumlah_betina___IDX__" class="{{ $in }}" placeholder="0"></div>
        </div>
        <p class="{{ $err }}" data-error-for="skkh_jumlah___IDX__"></p>
      </div>

      <div class="sm:col-span-2"><label for="keluhan_utama___IDX__" class="{{ $lbl }}">Keluhan utama <span class="text-red-500">*</span></label><textarea id="keluhan_utama___IDX__" data-field="keluhan_utama" class="{{ $ta }}" rows="3" placeholder="Jelaskan keluhan atau kondisi hewan ini"></textarea><p class="{{ $err }}" data-error-for="keluhan_utama___IDX__"></p></div>
      <div class="sm:col-span-2"><label for="info_tambahan___IDX__" class="{{ $lbl }}">Informasi tambahan <span class="font-normal text-muted-foreground">(opsional)</span></label><textarea id="info_tambahan___IDX__" class="{{ $ta }}" rows="2" placeholder="Informasi lain yang perlu diketahui petugas"></textarea></div>
    </div>
  </div>
</template>

<script>
(function () {
  'use strict';
  var SKKH = @json($cfg['skkh']), URL_SIMPAN = @json(route('sipandai.store')), TOKEN = @json(csrf_token());
  var $ = function (id) { return document.getElementById(id); };
  var form = $('form-pendaftaran'), list = $('hewan-list'), tpl = $('tpl-hewan').innerHTML, busy = false, counter = 0;
  var M = window.Motion;
  var calm = matchMedia('(prefers-reduced-motion:reduce)').matches || document.body.classList.contains('a11y-noanim');
  function icons() { if (window.lucide) lucide.createIcons(); }
  function fx(el, from, to, o) { if (M && !calm) M.animate(el, {opacity: [from.o, to.o], transform: [from.t, to.t]}, Object.assign({duration: .35, ease: [.2, .7, .2, 1]}, o || {})); }
  function val(id) { var e = $(id); return e ? e.value : ''; }
  function radio(name) { var c = form.querySelector('input[name="' + name + '"]:checked'); return c ? c.value : ''; }

  function setErr(id, msg, radioName) {
    var e = form.querySelector('[data-error-for="' + id + '"]'), i = $(id);
    if (e) { e.textContent = msg || ''; msg ? e.setAttribute('data-show', '') : e.removeAttribute('data-show'); }
    if (i) { msg ? i.setAttribute('aria-invalid', 'true') : i.removeAttribute('aria-invalid'); }
    if (radioName) form.querySelectorAll('input[name="' + radioName + '"]').forEach(function (r) { var s = r.nextElementSibling; if (s) s.style.borderColor = msg ? '#ef4444' : ''; });
  }

  // ---- validator = port dari JavaScript.html (GAS) ----
  var V_PEMILIK = {
    nama_pemilik: function (v) { return v.trim() ? null : T('Nama lengkap wajib diisi.'); },
    nik: function (v) { return !v.trim() ? T('NIK wajib diisi.') : (!/^\d{16}$/.test(v.trim()) ? T('NIK harus terdiri dari 16 digit angka.') : null); },
    alamat: function (v) { return v.trim() ? null : T('Alamat wajib diisi.'); },
    no_hp: function (v) { var c = v.trim().replace(/[\s-]/g, ''); return !c ? T('Nomor HP/WhatsApp wajib diisi.') : (!/^(\+62|62|0)8\d{7,12}$/.test(c) ? T('Format nomor HP tidak valid.') : null); }
  };
  var V_HEWAN = {
    nama_hewan: function (v) { return v.trim() ? null : T('Nama hewan wajib diisi.'); },
    jenis_hewan: function (v) { return v ? null : T('Jenis hewan wajib dipilih.'); },
    jenis_kelamin: function (v) { return v ? null : T('Jenis kelamin hewan wajib dipilih.'); },
    umur_tgl_lahir: function (v) { return v.trim() ? null : T('Umur atau tanggal lahir wajib diisi.'); },
    jenis_pelayanan: function (v) { return v ? null : T('Jenis pelayanan wajib dipilih.'); },
    keluhan_utama: function (v) { return v.trim() ? null : T('Keluhan utama wajib diisi.'); }
  };
  var V_SKKH = {
    skkh_tanggal: function (v) { return v.trim() ? null : T('Hari/tanggal wajib diisi.'); },
    skkh_nama_penerima: function (v) { return v.trim() ? null : T('Nama penerima wajib diisi.'); },
    skkh_alamat_penerima: function (v) { return v.trim() ? null : T('Alamat penerima wajib diisi.'); }
  };
  function isSkkh(i) { return val('jenis_pelayanan_' + i) === SKKH; }
  function jumlahOk(i) {
    var j = parseInt(val('skkh_jumlah_jantan_' + i), 10) || 0, b = parseInt(val('skkh_jumlah_betina_' + i), 10) || 0;
    var e = (j + b <= 0) ? T('Isi jumlah jantan dan/atau betina, minimal 1 ekor.') : null;
    setErr('skkh_jumlah_' + i, e); return !e;
  }
  function vHewanField(i, f) {
    var v = f === 'jenis_kelamin' ? radio('jenis_kelamin_' + i) : val(f + '_' + i), e = V_HEWAN[f](v);
    setErr(f + '_' + i, e, f === 'jenis_kelamin' ? 'jenis_kelamin_' + i : null); return !e;
  }
  function vCard(i) {
    var ok = true;
    Object.keys(V_HEWAN).forEach(function (f) { if (!vHewanField(i, f)) ok = false; });
    if (isSkkh(i)) {
      Object.keys(V_SKKH).forEach(function (f) { var e = V_SKKH[f](val(f + '_' + i)); setErr(f + '_' + i, e); if (e) ok = false; });
      if (!jumlahOk(i)) ok = false;
    }
    return ok;
  }
  function indexes() { return [].map.call(list.querySelectorAll('.hewan-card'), function (c) { return c.getAttribute('data-hewan-index'); }); }

  Object.keys(V_PEMILIK).forEach(function (n) {
    var e = $(n); if (!e) return;
    ['blur', 'change'].forEach(function (ev) { e.addEventListener(ev, function () { setErr(n, V_PEMILIK[n](e.value)); }); });
  });

  // ---- kartu hewan ----
  function renumber() {
    var cs = list.querySelectorAll('.hewan-card');
    cs.forEach(function (c, k) { c.querySelector('.hewan-no .lbl').textContent = T('Hewan') + ' ' + (k + 1); c.querySelector('.btn-hapus').style.display = cs.length > 1 ? '' : 'none'; });
    $('badge-jumlah').textContent = cs.length + ' ' + T('hewan');
  }
  function toggleSkkh(i) {
    var b = list.querySelector('[data-skkh-block="' + i + '"]'), on = isSkkh(i);
    if (!b) return;
    if (on && b.hidden) { b.hidden = false; fx(b, {o: 0, t: 'translateY(-8px)'}, {o: 1, t: 'none'}); }
    if (!on) { b.hidden = true; Object.keys(V_SKKH).forEach(function (f) { setErr(f + '_' + i, null); }); setErr('skkh_jumlah_' + i, null); }
  }
  function addCard() {
    var i = ++counter, w = document.createElement('div');
    w.innerHTML = tpl.split('__IDX__').join(i).trim();
    var c = w.firstElementChild; list.appendChild(c); icons();
    Object.keys(V_HEWAN).forEach(function (f) {
      c.querySelectorAll('[data-field="' + f + '"]').forEach(function (e) { ['blur', 'change'].forEach(function (ev) { e.addEventListener(ev, function () { vHewanField(i, f); }); }); });
    });
    c.querySelector('#jenis_pelayanan_' + i).addEventListener('change', function () { toggleSkkh(i); });
    Object.keys(V_SKKH).forEach(function (f) { var e = c.querySelector('#' + f + '_' + i); e.addEventListener('blur', function () { setErr(e.id, V_SKKH[f](e.value)); }); });
    ['jantan', 'betina'].forEach(function (k) { c.querySelector('#skkh_jumlah_' + k + '_' + i).addEventListener('blur', function () { jumlahOk(i); }); });
    c.querySelector('.btn-hapus').addEventListener('click', function () {
      var done = function () { c.remove(); renumber(); };
      if (M && !calm) M.animate(c, {opacity: [1, 0], transform: ['none', 'translateX(24px)']}, {duration: .22}).finished.then(done); else done();
    });
    renumber(); fx(c, {o: 0, t: 'translateY(14px)'}, {o: 1, t: 'none'});
    return c;
  }
  $('btn-tambah-hewan').addEventListener('click', function () { var c = addCard(); c.scrollIntoView({behavior: calm ? 'auto' : 'smooth', block: 'center'}); });
  addCard();

  // ---- tampilan ----
  function step(n) {
    document.querySelectorAll('#stepper [data-step]').forEach(function (li) {
      var s = +li.dataset.step, on = s <= n, d = li.querySelector('.step-dot');
      d.classList.toggle('bg-padi', on); d.classList.toggle('text-white', on); d.classList.toggle('border-padi', on);
      li.querySelector('span:last-child').classList.toggle('text-foreground', on);
    });
  }
  step(1);
  function struk(r) {
    form.hidden = true; $('view-intro').hidden = true; $('view-error').hidden = true; $('view-struk').hidden = false;
    $('s-lokasi').textContent = r.lokasi; $('s-no').textContent = r.noPendaftaran; $('s-tgl').textContent = r.tanggal; $('s-waktu').textContent = r.waktu;
    $('s-pemilik').textContent = r.pemilik; $('s-nik').textContent = r.nikSamar;
    $('s-ket').textContent = r.jumlahHewan > 1 ? r.jumlahHewan + ' ' + T('ekor hewan didaftarkan dengan nomor') : T('Nomor pendaftaran Anda');
    var ul = $('s-hewan'); ul.innerHTML = '';
    r.hewan.forEach(function (h) {
      var li = document.createElement('li'); li.className = 'flex items-center justify-between gap-3 rounded-md border border-border bg-muted/40 px-3 py-2';
      var a = document.createElement('span'); a.innerHTML = '<b></b> <span class="text-xs text-muted-foreground"></span>';
      a.querySelector('b').textContent = h.nama; a.querySelector('span').textContent = '· ' + h.jenis;
      var b = document.createElement('span'); b.className = 'rounded-full bg-padi/10 px-2 py-0.5 text-[11px] font-semibold text-padi dark:text-blue-300'; b.textContent = h.pelayanan;
      li.appendChild(a); li.appendChild(b); ul.appendChild(li);
    });
    step(3); icons(); scrollTo({top: 0, behavior: calm ? 'auto' : 'smooth'});
    fx($('struk'), {o: 0, t: 'translateY(24px) scale(.97)'}, {o: 1, t: 'none'}, {duration: .5});
    if (M && !calm) M.animate($('struk-check'), {transform: ['scale(.4)', 'scale(1)']}, {duration: .6, ease: [.2, 1.4, .4, 1], delay: .25});
  }
  function showErr(msg) { $('view-error').hidden = false; $('error-desc').textContent = msg || T('Terjadi kendala saat menyimpan data. Periksa koneksi internet lalu coba lagi.'); icons(); scrollTo({top: 0, behavior: 'smooth'}); }
  function busyState(b) { busy = b; var t = $('btn-submit'); t.disabled = b; t.querySelector('.spin').classList.toggle('hidden', !b); t.querySelector('.lbl').textContent = b ? T('Mengirim…') : T('Kirim pendaftaran'); }

  $('btn-daftar-lagi').onclick = function () {
    form.reset(); Object.keys(V_PEMILIK).forEach(function (n) { setErr(n, null); }); list.innerHTML = ''; addCard();
    $('view-struk').hidden = true; form.hidden = false; $('view-intro').hidden = false; step(1); scrollTo({top: 0, behavior: 'smooth'});
  };
  $('btn-cetak').onclick = function () { window.print(); };
  $('btn-salin').onclick = function () {
    var t = $('s-no').textContent, l = this.querySelector('span');
    (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).then(function () { l.textContent = T('Tersalin'); setTimeout(function () { l.textContent = T('Salin nomor'); }, 1600); }).catch(function () { l.textContent = T('Salin manual'); });
  };

  form.addEventListener('submit', function (e) {
    e.preventDefault(); if (busy) return;
    var okP = true; Object.keys(V_PEMILIK).forEach(function (n) { var er = V_PEMILIK[n](val(n)); setErr(n, er); if (er) okP = false; });
    var okH = true; indexes().forEach(function (i) { if (!vCard(i)) okH = false; });
    if (!okP || !okH) { var f = form.querySelector('[aria-invalid="true"]'); if (f) f.scrollIntoView({behavior: 'smooth', block: 'center'}); return; }

    var body = {
      website: val('hp'),
      pemilik: {nama_pemilik: val('nama_pemilik').trim(), nik: val('nik').trim(), alamat: val('alamat').trim(), no_hp: val('no_hp').trim()},
      hewan: indexes().map(function (i) { return {
        nama_hewan: val('nama_hewan_' + i).trim(), jenis_hewan: val('jenis_hewan_' + i), ras: val('ras_' + i).trim(), jenis_kelamin: radio('jenis_kelamin_' + i),
        umur_tgl_lahir: val('umur_tgl_lahir_' + i).trim(), ciri_khusus: val('ciri_khusus_' + i).trim(), jenis_pelayanan: val('jenis_pelayanan_' + i),
        keluhan_utama: val('keluhan_utama_' + i).trim(), info_tambahan: val('info_tambahan_' + i).trim(),
        skkh_tanggal: val('skkh_tanggal_' + i), skkh_nama_penerima: val('skkh_nama_penerima_' + i).trim(), skkh_alamat_penerima: val('skkh_alamat_penerima_' + i).trim(),
        skkh_jumlah_jantan: val('skkh_jumlah_jantan_' + i), skkh_jumlah_betina: val('skkh_jumlah_betina_' + i)
      }; })
    };
    busyState(true);
    fetch(URL_SIMPAN, {method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN}, body: JSON.stringify(body)})
      .then(function (r) { return r.json().catch(function () { return {message: r.status === 419 ? T('Sesi habis. Muat ulang halaman lalu kirim lagi.') : (r.status === 429 ? T('Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi.') : null)}; }); })
      .then(function (d) { busyState(false); d && d.success ? struk(d) : showErr(d && d.message); })
      .catch(function () { busyState(false); showErr(); });
  });
  icons();
})();
</script>
@endsection
