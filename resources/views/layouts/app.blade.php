<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<script>try{var t=localStorage.getItem("theme");if(t==="dark"||(!t&&matchMedia("(prefers-color-scheme: dark)").matches))document.documentElement.classList.add("dark")}catch(e){}</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>function T(s){return s}</script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Dinas Pertanian Kota Padang')</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/opendyslexic/index.css">
<meta name="description" content="@yield('desc', 'Portal resmi Dinas Pertanian Kota Padang: layanan publik, berita, dan informasi pertanian.')">
<meta property="og:type" content="website">
<meta property="og:title" content="@yield('title', 'Dinas Pertanian Kota Padang')">
<meta property="og:description" content="@yield('desc', 'Portal resmi Dinas Pertanian Kota Padang')">
<meta property="og:image" content="@yield('og')">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Familjen+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
<script src="https://cdn.tailwindcss.com?plugins=typography"></script>
<script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif'],display:['"Familjen Grotesk"','Inter','sans-serif']},colors:{border:'hsl(var(--border) / <alpha-value>)',input:'hsl(var(--input) / <alpha-value>)',background:'hsl(var(--background) / <alpha-value>)',foreground:'hsl(var(--foreground) / <alpha-value>)',card:{DEFAULT:'hsl(var(--card) / <alpha-value>)',foreground:'hsl(var(--card-foreground) / <alpha-value>)'},muted:{DEFAULT:'hsl(var(--muted) / <alpha-value>)',foreground:'hsl(var(--muted-foreground) / <alpha-value>)'},primary:{DEFAULT:'hsl(var(--primary) / <alpha-value>)',foreground:'hsl(var(--primary-foreground) / <alpha-value>)'},padi:{DEFAULT:'#1F4FBF',50:'#EEF3FF',100:'#DCE7FF',200:'#C7D8FF',300:'#A9C0FF',700:'#173E9C',900:'#0B2A6B'},langit:{DEFAULT:'#0D47A1',50:'#EAF1FF',900:'#08285C'},emas:{DEFAULT:'#F2B93B',100:'#FDF1CF'}}}}}</script>
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
<div id="pg">
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-2 focus:top-2 focus:z-[200] focus:rounded focus:bg-white focus:p-3">Lewati ke konten</a>
@include('partials.nav')
<div id="main" tabindex="-1">@yield('content')</div>
<footer class="ft">
  <div class="bg">DINAS PERTANIAN</div>
  <div class="wrap">
    <div class="cols">
      <div><a class="logo"><b>PADANG</b><small>Dinas Pertanian Kota Padang</small></a>
        <p style="margin-top:14px"><i class="bi bi-geo-alt"></i> {{ \App\Models\Setting::get('alamat') }}<br><i class="bi bi-envelope"></i> Email : {{ \App\Models\Setting::get('email') }}<br><i class="bi bi-telephone"></i> Telp. : {{ \App\Models\Setting::get('telepon') }}</p></div>
      <div style="text-align:right"><h4>LAYANAN PUBLIK</h4><a href="#">SIPETANI</a><a href="#">E-Pupuk</a><a href="#">Klinik Tani</a></div>
      <div><h4>BERITA DINAS</h4><a href="#">FLAYER</a></div>
      <div style="text-align:right"><h4>PPID</h4><a href="{{ url('/halaman/profil-ppid') }}">Profil PPID</a><a href="#">Alur Layanan Informasi Publik</a><a href="{{ url('/ppid/permohonan') }}">Permohonan Informasi Publik</a><a href="#">Pengajuan Keberatan</a></div>
    </div>
    <h4 style="margin-top:50px;font-size:17px">PROFIL DINAS PERTANIAN</h4>
    <p style="text-align:center;margin-top:40px;font-size:16px"><i class="bi bi-android2" style="color:#7cb342"></i> &nbsp;<i class="bi bi-apple"></i> &nbsp;<a href="{{ \App\Models\Setting::get('facebook', '#') }}" aria-label="Facebook"><i class="bi bi-facebook" style="color:#3b5998"></i></a> &nbsp;<a href="{{ \App\Models\Setting::get('instagram', '#') }}" aria-label="Instagram"><i class="bi bi-instagram" style="color:#dd2a7b"></i></a> &nbsp;<a href="{{ \App\Models\Setting::get('youtube', '#') }}" aria-label="YouTube"><i class="bi bi-youtube" style="color:#e53935"></i></a></p>
    <div class="copy">@php [$vt, $vd] = \App\Models\Visit::stats(); @endphp Total Pengunjung : <b>{{ number_format($vt) }}</b> &nbsp; Hari ini : <b>{{ number_format($vd) }}</b><br>© 2023 - {{ date('Y') }} Dinas Pertanian Kota Padang. All rights reserved.</div>
  </div>
</footer>
</div>
@include('partials.a11y')
@include('partials.ui')
</body>
</html>
