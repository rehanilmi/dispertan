@extends('layouts.app')
@section('title', 'Monitoring Kelompok Tani - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Monitoring Kelompok Tani'])
@php $warna = ['#1F6B45', '#12408A', '#E8B730', '#B3261E', '#0891B2', '#EA580C', '#65A30D', '#DB2777', '#475569', '#92400E', '#7C3AED']; @endphp
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<main class="wrap py-8">
  <div class="grid gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
    <section>
      <h2 class="mb-3 font-display text-xl font-bold">Kelompok tani per kecamatan</h2>
      <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-xs text-slate-600"><tr><th class="p-3 text-left">Kecamatan</th><th class="p-3 text-right">Jumlah Kelompok Tani</th><th class="p-3 text-right">Anggota Laki-laki</th><th class="p-3 text-right">Anggota Perempuan</th></tr></thead>
          <tbody id="tb">
            @forelse ($rows as $i => $r)
              <tr data-kec="{{ $r->kecamatan }}" tabindex="0" class="cursor-pointer border-t border-slate-100 transition hover:bg-padi-50 focus:bg-padi-50 focus:outline-none">
                <td class="p-3 font-semibold"><i class="mr-2 inline-block h-2.5 w-2.5 rounded-full" style="background:{{ $warna[$i % count($warna)] }}"></i>{{ $r->kecamatan }}</td>
                <td class="p-3 text-right">{{ number_format($r->kelompok, 0, ',', '.') }}</td><td class="p-3 text-right">{{ number_format($r->l, 0, ',', '.') }}</td><td class="p-3 text-right">{{ number_format($r->p, 0, ',', '.') }}</td>
              </tr>
            @empty<tr><td colspan="4" class="p-8 text-center text-slate-400">Data kelompok tani belum tersedia.</td></tr>@endforelse
          </tbody>
          @if($rows->isNotEmpty())<tfoot class="border-t-2 border-slate-200 bg-slate-50 font-bold"><tr><td class="p-3">Kota Padang</td><td class="p-3 text-right">{{ number_format($tot['kelompok'], 0, ',', '.') }}</td><td class="p-3 text-right">{{ number_format($tot['l'], 0, ',', '.') }}</td><td class="p-3 text-right">{{ number_format($tot['p'], 0, ',', '.') }}</td></tr></tfoot>@endif
        </table>
      </div>
      <p class="mt-3 text-xs leading-relaxed text-slate-500">Klik nama kecamatan untuk memfokuskan peta. Jumlah anggota laki-laki dan perempuan dihitung dari data anggota yang sudah masuk ke sistem.</p>
    </section>
    <section>
      <div class="mb-3 flex items-center justify-between"><h2 class="font-display text-xl font-bold">Peta sebaran</h2><span class="text-xs text-slate-500" id="info">{{ $titik->count() }} kelompok memiliki titik koordinat</span></div>
      <div id="map" class="isolate h-[560px] rounded-2xl border border-slate-100 shadow-[0_10px_25px_rgba(0,0,0,0.05)]" role="application" aria-label="Peta sebaran kelompok tani"></div>
      <p class="mt-2 text-xs text-slate-500">Titik mewakili satu lokasi anggota dan dibulatkan (±100 m) untuk menjaga privasi anggota.</p>
    </section>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
  var data = @json($titik), pal = @json($warna), kecs = @json($rows->pluck('kecamatan')->values());
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); };
  var map = L.map('map').setView([-0.9471, 100.4172], 11);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(map);
  var ms = data.map(function (k) {
    var c = pal[Math.max(0, kecs.indexOf(k.kec)) % pal.length], n = function (v) { return Number(v).toLocaleString('id-ID'); };
    var html = '<div style="min-width:220px"><b style="font-size:14px">' + esc(k.nama) + '</b><br><small>' + T('ID Poktan') + ' ' + esc(k.id || '-') + '</small>' +
      '<table style="font-size:12px;margin-top:6px"><tr><td>' + T('Kecamatan') + '</td><td>&nbsp;' + esc(k.kec) + '</td></tr><tr><td>' + T('Desa/Kel.') + '</td><td>&nbsp;' + esc(k.desa || '-') + '</td></tr><tr><td>' + T('Ketua') + '</td><td>&nbsp;' + esc(k.ketua || '-') + '</td></tr>' +
      '<tr><td>' + T('Anggota') + '</td><td>&nbsp;' + n(k.anggota) + (k.terdata ? ' (' + T('terdata') + ' ' + n(k.terdata) + ': ' + k.l + ' L, ' + k.p + ' P)' : '') + '</td></tr>' +
      (k.luas ? '<tr><td>' + T('Luas lahan') + '</td><td>&nbsp;' + n(k.luas) + ' ha</td></tr>' : '') + (k.komoditas ? '<tr><td>' + T('Komoditas') + '</td><td>&nbsp;' + esc(k.komoditas) + '</td></tr>' : '') + '</table></div>';
    var m = L.circleMarker([k.lat, k.lng], {radius: 9, color: '#fff', weight: 2.5, fillColor: c, fillOpacity: .92}).bindPopup(html).addTo(map);
    m.kec = k.kec; return m;
  });
  function fokus(kec) {
    var v = [];
    ms.forEach(function (m) { var ok = !kec || m.kec === kec; ok ? m.addTo(map) : map.removeLayer(m); if (ok) v.push(m.getLatLng()); });
    if (v.length) map.fitBounds(L.latLngBounds(v).pad(.25), {maxZoom: 15});
    document.getElementById('info').textContent = kec ? v.length + ' ' + T('titik di') + ' ' + kec : ms.length + ' ' + T('kelompok memiliki titik koordinat');
  }
  if (ms.length) map.fitBounds(L.latLngBounds(ms.map(function (m) { return m.getLatLng(); })).pad(.15));
  var aktif = null;
  document.querySelectorAll('#tb tr[data-kec]').forEach(function (tr) {
    function pilih() {
      var k = tr.dataset.kec;
      document.querySelectorAll('#tb tr').forEach(function (x) { x.style.background = ''; });
      if (aktif === k) { aktif = null; fokus(null); return; }
      aktif = k; tr.style.background = '#EEF6F1'; fokus(k);
    }
    tr.onclick = pilih; tr.onkeydown = function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pilih(); } };
  });
})();
</script>
@endsection
