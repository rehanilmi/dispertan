<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<script src="{{ asset('vendor/motion.js') }}"></script>
<script>
(function () {
  function safe(fn) { try { fn(); } catch (e) { if (window.console) console.warn('ui:', e); } }

  // 1) ikon Lucide (dijalankan pertama; tidak bergantung pada animasi)
  safe(function () { if (window.lucide) lucide.createIcons(); });

  // 2) mode terang / gelap
  safe(function () {
    var tb = document.getElementById('themeBtn'); if (!tb) return;
    tb.setAttribute('aria-pressed', document.documentElement.classList.contains('dark'));
    tb.onclick = function () {
      var d = document.documentElement.classList.toggle('dark');
      try { localStorage.setItem('theme', d ? 'dark' : 'light'); } catch (e) {}
      tb.setAttribute('aria-pressed', d);
    };
  });

  // 3) animasi Motion = tambahan saja. Konten selalu terlihat; animasi hanya memudarkan masuk saat grup terlihat di layar.
  var M = window.Motion;
  var calm = matchMedia('(prefers-reduced-motion:reduce)').matches || document.body.classList.contains('a11y-noanim');
  if (!M || calm) return;

  safe(function () {
    [].slice.call(document.querySelectorAll('[data-stagger]')).forEach(function (g) {
      M.inView(g, function () {
        var kids = [].slice.call(g.children);
        var a = M.animate(kids, {opacity: [0, 1], transform: ['translateY(16px)', 'translateY(0px)']}, {duration: .5, delay: M.stagger(.06), ease: [.2, .7, .2, 1]});
        // setelah selesai, lepas transform inline agar efek hover kartu (Tailwind) tetap bekerja
        if (a && a.then) a.then(function () { kids.forEach(function (k) { k.style.transform = ''; k.style.opacity = ''; }); }, function () {});
      }, {margin: '0px 0px -6% 0px'});
    });
  });
  safe(function () {
    [].slice.call(document.querySelectorAll('[data-count]')).forEach(function (el) {
      var n = +el.getAttribute('data-count') || 0, fin = el.textContent;
      M.inView(el, function () {
        var a = M.animate(0, n, {duration: 1.3, ease: 'easeOut', onUpdate: function (v) { el.textContent = Math.round(v).toLocaleString('id-ID'); }});
        if (a && a.then) a.then(function () { el.textContent = fin; }, function () { el.textContent = fin; });
      });
    });
  });
})();
</script>
