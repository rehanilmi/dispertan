<button id="a11yBtn" class="a11y-fab" aria-label="Buka Menu Aksesibilitas (Ctrl+U)" title="Menu Aksesibilitas (Ctrl+U)"><i class="bi bi-person-wheelchair"></i></button>
<div id="a11yModal" class="a11y-back" hidden>
  <div class="a11y-box" role="dialog" aria-modal="true" aria-labelledby="a11yTitle">
    <div class="a11y-head"><h2 id="a11yTitle">Menu Aksesibilitas (CTRL+U)</h2><button id="a11yClose" aria-label="Tutup">&times;</button></div>
    <div class="a11y-grid">
      @foreach ([
        ['contrast','bi-circle-half','Kontras +'],['links','bi-link-45deg','Sorot Tautan'],
        ['text','bi-type','Teks Lebih Besar'],['space','bi-text-paragraph','Spasi Teks'],
        ['noanim','bi-pause-circle','Animasi Dijeda'],['noimg','bi-image','Sembunyikan Gambar'],
        ['dyslexia','bi-fonts','Ramah Disleksia'],['cursor','bi-cursor','Kursor Besar'],
      ] as [$k,$i,$t])
        <button class="a11y-card" data-a="{{ $k }}" aria-pressed="false"><i class="bi {{ $i }}"></i><span>{{ $t }}</span></button>
      @endforeach
      <button class="a11y-card reset" id="a11yReset"><i class="bi bi-arrow-counterclockwise"></i><span>Reset</span></button>
    </div>
  </div>
</div>
<script>
(function(){
  var K='a11y',b=document.body,m=document.getElementById('a11yModal'),fab=document.getElementById('a11yBtn');
  var st=[];try{st=JSON.parse(localStorage.getItem(K)||'[]')}catch(e){}
  function apply(){
    document.querySelectorAll('[data-a]').forEach(function(c){
      var on=st.indexOf(c.dataset.a)>-1;b.classList.toggle('a11y-'+c.dataset.a,on);
      c.classList.toggle('on',on);c.setAttribute('aria-pressed',on);
    });
    try{localStorage.setItem(K,JSON.stringify(st))}catch(e){}
  }
  function open(){m.hidden=false;document.getElementById('a11yClose').focus()}
  function close(){m.hidden=true;fab.focus()}
  document.querySelectorAll('[data-a]').forEach(function(c){c.onclick=function(){
    var i=st.indexOf(c.dataset.a);i>-1?st.splice(i,1):st.push(c.dataset.a);apply()}});
  document.getElementById('a11yReset').onclick=function(){st=[];apply()};
  fab.onclick=open;document.getElementById('a11yClose').onclick=close;
  m.onclick=function(e){if(e.target===m)close()};
  document.addEventListener('keydown',function(e){
    if(e.ctrlKey&&!e.shiftKey&&e.key.toLowerCase()==='u'){e.preventDefault();m.hidden?open():close()}
    else if(e.key==='Escape'&&!m.hidden)close();
  });
  apply();
})();
</script>
