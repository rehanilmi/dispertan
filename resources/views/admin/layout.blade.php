<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title') - Admin Dispertan</title>
<script>function T(s){return s}</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.1.1/dist/trix.css">
<script src="https://cdn.jsdelivr.net/npm/trix@2.1.1/dist/trix.umd.min.js"></script>
<script>addEventListener("trix-file-accept",function(e){e.preventDefault()})</script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" target="_blank">Lihat Website</a></li>
    </ul>
    <a href="{{ route('admin.profil') }}" class="navbar-text small ml-auto mr-3 d-none d-md-inline text-dark" title="Profil Saya">
      @if(auth()->user()->foto)<img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="" style="height:28px;width:28px;border-radius:50%;object-fit:cover;vertical-align:middle">@else<i class="fas fa-user-circle"></i>@endif
      {{ auth()->user()->name }} · {{ \App\Support\Akses::label(auth()->user()) }}</a>
    <div class="btn-group btn-group-sm mr-2" role="group" aria-label="Pilihan bahasa">
      @foreach (['id' => 'ID', 'en' => 'EN'] as $kode => $lbl)<a href="{{ route('bahasa', $kode) }}" lang="{{ $kode }}" class="btn {{ app()->getLocale() === $kode ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $lbl }}</a>@endforeach
    </div>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-danger">Keluar</button></form>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link"><span class="brand-text font-weight-light pl-3">Admin Dispertan</span></a>
    <div class="sidebar"><nav class="mt-2"><ul class="nav nav-pills nav-sidebar flex-column">
      <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
      <li class="nav-item"><a href="{{ route('admin.profil') }}" class="nav-link {{ request()->routeIs('admin.profil*') ? 'active' : '' }}"><i class="nav-icon fas fa-id-card"></i><p>Profil Saya</p></a></li>
      @foreach (\App\Support\Akses::sidebar(auth()->user()) as $grup => $items)
        <li class="nav-header" style="text-transform:uppercase">{{ $grup }}</li>
        @foreach ($items as $m)
          <li class="nav-item"><a href="{{ \App\Support\Akses::url($m) }}" class="nav-link {{ \App\Support\Akses::aktif($m) ? 'active' : '' }}"><i class="nav-icon fas {{ $m['icon'] }}"></i><p>{{ $m['label'] }}</p></a></li>
        @endforeach
      @endforeach
    </ul></nav></div>
  </aside>
  <div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>@yield('title')</h1></div></section>
    <section class="content"><div class="container-fluid">
      @unless(file_exists(public_path('storage')))<div class="alert alert-warning"><b>Tautan storage belum dibuat.</b> Gambar dan berkas unggahan tidak akan tampil di website. Jalankan <code>php artisan storage:link</code> di server.</div>@endunless
      @if($errors->any())<div class="alert alert-danger"><b>Data belum tersimpan:</b><ul class="mb-0 pl-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
      @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
      @yield('content')
    </div></section>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
