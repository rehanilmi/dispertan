@php
use App\Models\{Layanan, Menu, Setting, Unit};
$tops = Menu::whereNull('parent_id')->orderBy('urutan')->with('children')->get();
$U = Unit::orderBy('urutan')->get();
$L = Layanan::orderBy('urutan')->get();
$href = fn ($u) => str_starts_with((string) $u, '/') ? url($u) : ($u ?: '#');
$expand = fn ($c) => match ($c->url) {
    '@bidang'  => $U->where('tipe', 'bidang')->map(fn ($u) => [$u->nama, $u->url])->values()->all(),
    '@uptd'    => $U->where('tipe', 'uptd')->map(fn ($u) => [$u->nama, $u->url])->values()->all(),
    '@layanan' => $L->map(fn ($x) => [$x->nama, $x->url ?: '#'])->values()->all(),
    default    => [[$c->label, $c->url]],
};
$logo = Setting::get('logo');
@endphp
<div class="hdr">
  <div class="wrap nav">
    <a href="{{ route('home') }}" class="logo">
      @if($logo)<img src="{{ asset('storage/'.$logo) }}" alt="{{ Setting::get('nama_dinas', 'Dinas Pertanian Kota Padang') }}" style="height:46px;width:auto">
      @else<b>PADANG</b><small>{{ Setting::get('nama_dinas', 'Dinas Pertanian Kota Padang') }}</small>@endif
    </a>
    <button class="burger" aria-label="Buka menu" aria-expanded="false"><i data-lucide="menu" class="h-6 w-6"></i></button>
    <nav class="mn" aria-label="Menu utama">
      @foreach ($tops as $top)
        @php $cols = $top->children->groupBy(fn ($c) => $c->kolom ?: $top->label)->map(fn ($g) => $g->flatMap($expand)->values()->all()); @endphp
        <div class="mi {{ $cols->isNotEmpty() ? 'has' : '' }}">
          <a class="m" href="{{ $href($top->url) }}">{{ $top->label }} @if($cols->isNotEmpty())<i data-lucide="chevron-down" class="h-3.5 w-3.5"></i>@endif</a>
          @if($cols->isNotEmpty())
            <div class="mega"><div class="wrap mg">
              @foreach ($cols as $title => $links)
                <div><h5>{{ $title }}</h5>
                  @forelse ($links as [$t, $u])<a href="{{ $href($u) }}">{{ $t }}</a>@empty<span class="mut">Belum ada data</span>@endforelse
                </div>
              @endforeach
            </div></div>
          @endif
        </div>
      @endforeach
    </nav>
    <div class="langsw inline-flex shrink-0 overflow-hidden rounded-md border border-border text-xs font-bold" role="group" aria-label="Pilihan bahasa">
      @foreach (['id' => 'ID', 'en' => 'EN'] as $kode => $lbl)
        <a href="{{ route('bahasa', $kode) }}" lang="{{ $kode }}" hreflang="{{ $kode }}" @if(app()->getLocale() === $kode) aria-current="true" @endif class="px-2.5 py-2 transition {{ app()->getLocale() === $kode ? 'bg-padi text-white' : 'bg-background text-foreground hover:bg-muted' }}">{{ $lbl }}</a>
      @endforeach
    </div>
    <button type="button" id="themeBtn" class="themebtn grid h-9 w-9 shrink-0 place-items-center rounded-md border border-border bg-background text-foreground transition hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-padi/50" aria-label="Ganti mode terang atau gelap" aria-pressed="false"><i data-lucide="sun" class="h-4 w-4 dark:hidden"></i><i data-lucide="moon" class="hidden h-4 w-4 dark:block"></i></button>
  </div>
</div>
<script>
(function () {
  var wide = window.matchMedia('(min-width:1101px)');
  document.querySelectorAll('.mi.has').forEach(function (mi) {
    var t;
    function buka() { clearTimeout(t); document.querySelectorAll('.mi[data-open]').forEach(function (o) { if (o !== mi) delete o.dataset.open; }); mi.dataset.open = '1'; }
    function tutup() { clearTimeout(t); t = setTimeout(function () { delete mi.dataset.open; }, 130); }
    mi.addEventListener('mouseenter', function () { if (wide.matches) buka(); });
    mi.addEventListener('mouseleave', function () { if (wide.matches) tutup(); });
    mi.firstElementChild.addEventListener('click', function (e) {
      if (!wide.matches) { e.preventDefault(); mi.classList.toggle('open'); }
    });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') document.querySelectorAll('.mi[data-open]').forEach(function (o) { delete o.dataset.open; }); });
  var b = document.querySelector('.burger');
  b.onclick = function () { var m = document.querySelector('.mn'); m.classList.toggle('open'); b.setAttribute('aria-expanded', m.classList.contains('open')); };
})();
</script>
