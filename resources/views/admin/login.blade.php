<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"></head>
<body class="hold-transition login-page">
<div class="login-box"><div class="card card-outline card-primary">
  <div class="card-header text-center"><b class="h4">Admin Dispertan Padang</b></div>
  <div class="card-body">
    <form method="post" action="{{ url('admin/login') }}">@csrf
      <div class="input-group mb-3"><input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required autofocus>
        <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
      <div class="input-group mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required>
        <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div></div>
      <div class="row"><div class="col-8"><label><input type="checkbox" name="remember"> Ingat saya</label></div>
        <div class="col-4"><button class="btn btn-primary btn-block">Masuk</button></div></div>
    </form>
    <p class="mb-0 mt-3 text-center small"><a href="{{ route('bahasa', 'id') }}">ID</a> | <a href="{{ route('bahasa', 'en') }}">EN</a></p>
  </div>
</div></div></body></html>
