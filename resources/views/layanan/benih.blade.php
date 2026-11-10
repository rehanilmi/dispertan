@extends('layouts.app')
@section('title', 'Benih Padi Bersertifikat - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Benih Padi Bersertifikat'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<main class="wrap py-8">
  <div class="mb-4 flex flex-wrap items-center gap-4 text-xs">
    <input id="cari" placeholder="Cari varietas (mis. Inpari 32)…" aria-label="Cari varietas" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
    <span><i class="inline-block h-3 w-3 rounded-full bg-padi"></i> Stok &gt; 1.000 kg</span>
    <span><i class="inline-block h-3 w-3 rounded-full bg-amber-500"></i> Stok terbatas</span>
    <span><i class="inline-block h-3 w-3 rounded-full bg-red-600"></i> Stok habis</span>
  </div>
  <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
    <div id="map" class="isolate h-[540px] rounded-2xl border border-slate-100 shadow-[0_10px_25px_rgba(0,0,0,0.05)]" role="application" aria-label="Peta supplier benih padi"></div>
    <ul id="daftar" class="max-h-[540px] space-y-2 overflow-y-auto"></ul>
  </div>
  <p class="mt-3 text-xs text-slate-400">Stok bersifat informasi dari supplier dan dapat berubah. Hubungi supplier sebelum berkunjung.</p>
</main>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
  var data=@json($suppliers);
  var esc=function(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]})};
  var map=L.map('map').setView([-0.9471,100.4172],11);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap'}).addTo(map);
  var list=document.getElementById('daftar');
  data.forEach(function(s){
    s.total=s.stok.reduce(function(a,b){return a+(+b.kg||0)},0);
    var color=s.total>1000?'#059669':(s.total>0?'#f59e0b':'#dc2626');
    var wa=String(s.kontak).replace(/\D/g,'').replace(/^0/,'62');
    var rows=s.stok.map(function(b){return '<tr><td>'+esc(b.varietas)+'</td><td>'+esc(b.kelas)+'</td><td style="text-align:right"><b>'+Number(b.kg).toLocaleString('id-ID')+'</b> kg</td></tr>'}).join('')||'<tr><td colspan=3>'+T('Belum ada data stok')+'</td></tr>';
    var html='<div style="min-width:230px"><b style="font-size:14px">'+esc(s.nama)+'</b><br><small>'+esc(s.alamat)+'</small>'+
      '<table style="width:100%;margin:8px 0;font-size:12px"><tr><th align=left>'+T('Jenis benih')+'</th><th align=left>'+T('Kelas')+'</th><th align=right>'+T('Stok')+'</th></tr>'+rows+'</table>'+
      (s.sertifikat?'<small>'+T('Sertifikat:')+' '+esc(s.sertifikat)+(s.berlaku?' ('+T('s.d.')+' '+esc(s.berlaku)+')':'')+'</small><br>':'')+
      '<small>'+T('Diperbarui:')+' '+esc(s.update)+'</small><br><a href="tel:'+esc(s.kontak)+'">'+esc(s.kontak)+'</a> &middot; <a target="_blank" rel="noopener" href="https://wa.me/'+wa+'">WhatsApp</a></div>';
    s.m=L.circleMarker([s.lat,s.lng],{radius:14,color:'#fff',weight:3,fillColor:color,fillOpacity:.95}).bindPopup(html).addTo(map);
    var li=document.createElement('li');
    li.innerHTML='<button type="button" class="w-full rounded-xl border border-slate-100 bg-white p-3 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"><b class="text-sm">'+esc(s.nama)+'</b><br><span class="text-xs text-slate-500">'+esc(s.alamat)+'</span><br><span class="text-xs font-semibold" style="color:'+color+'">Total stok '+s.total.toLocaleString('id-ID')+' kg</span></button>';
    li.firstChild.onclick=function(){map.setView([s.lat,s.lng],14);s.m.openPopup()};
    s.li=li;list.appendChild(li);
  });
  document.getElementById('cari').oninput=function(){
    var q=this.value.toLowerCase().trim();
    data.forEach(function(s){
      var ok=!q||s.stok.some(function(b){return String(b.varietas).toLowerCase().indexOf(q)>-1});
      s.li.style.display=ok?'':'none';
      ok?s.m.addTo(map):map.removeLayer(s.m);
    });
  };
})();
</script>
@endsection
