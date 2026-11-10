@extends('admin.layout')
@section('title', 'Profil Saya')
@section('content')
@php $fc = fn ($n) => 'form-control'.($errors->has($n) ? ' is-invalid' : ''); @endphp
<div class="row">
  <div class="col-lg-8">
    <form method="post" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
      <div class="card card-primary"><div class="card-header"><h3 class="card-title">Biodata</h3></div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group col-md-3 text-center">
              <div class="mb-2">@if($u->foto)<img id="prev" src="{{ asset('storage/'.$u->foto) }}" alt="Foto profil" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #DCE7FF">@else<span id="prevIcon" class="d-inline-flex align-items-center justify-content-center" style="width:120px;height:120px;border-radius:50%;background:#EEF3FF;color:#1F4FBF;font-size:54px"><i class="fas fa-user"></i></span><img id="prev" alt="" hidden style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #DCE7FF">@endif</div>
              <label class="btn btn-sm btn-outline-primary mb-1">Pilih foto<input type="file" id="foto" name="foto" accept="image/*" hidden></label>
              @if($u->foto)<div class="custom-control custom-checkbox small"><input type="checkbox" class="custom-control-input" id="hf" name="hapus_foto" value="1"><label class="custom-control-label" for="hf">Hapus foto</label></div>@endif
              <small class="d-block text-muted">JPG/PNG/WebP, maks. 3 MB</small>
            </div>
            <div class="col-md-9">
              <div class="form-group"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name', $u->name) }}" class="{{ $fc('name') }}"></div>
              <div class="form-row">
                <div class="form-group col-md-6"><label for="nip">NIP</label><input id="nip" name="nip" value="{{ old('nip', $u->nip) }}" class="{{ $fc('nip') }}" inputmode="numeric"></div>
                <div class="form-group col-md-6"><label for="jabatan">Jabatan</label><input id="jabatan" name="jabatan" value="{{ old('jabatan', $u->jabatan) }}" class="{{ $fc('jabatan') }}"></div>
              </div>
              <div class="form-group"><label for="no_hp">Nomor HP</label><input id="no_hp" name="no_hp" value="{{ old('no_hp', $u->no_hp) }}" class="{{ $fc('no_hp') }}" inputmode="tel"></div>
              <div class="form-group"><label for="alamat">Alamat</label><textarea id="alamat" name="alamat" rows="2" class="{{ $fc('alamat') }}">{{ old('alamat', $u->alamat) }}</textarea></div>
            </div>
          </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary">Simpan profil</button></div>
      </div>
    </form>
  </div>
  <div class="col-lg-4">
    <div class="card card-outline card-info"><div class="card-body small">
      <dl class="mb-0"><dt>Email (login)</dt><dd>{{ $u->email }}</dd><dt>Peran</dt><dd class="mb-0">{{ \App\Support\Akses::label($u) }}</dd></dl>
      <p class="text-muted mb-0 mt-2">Email, peran, bidang/UPTD, dan hak akses menu diatur oleh superadmin.</p>
    </div></div>
    <form method="post" action="{{ route('admin.profil.password') }}" autocomplete="off">@csrf @method('PUT')
      <div class="card"><div class="card-header"><h3 class="card-title">Ganti password</h3></div>
        <div class="card-body">
          <div class="form-group"><label for="pl">Password saat ini</label><input id="pl" type="password" name="password_lama" class="{{ $fc('password_lama') }}" autocomplete="current-password"></div>
          <div class="form-group"><label for="pb">Password baru (min. 10 karakter)</label><input id="pb" type="password" name="password" class="{{ $fc('password') }}" autocomplete="new-password"></div>
          <div class="form-group mb-0"><label for="pc">Ulangi password baru</label><input id="pc" type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
        </div>
        <div class="card-footer"><button class="btn btn-outline-primary">Ubah password</button></div>
      </div>
    </form>
  </div>
</div>
<script>
(function () {
  var f = document.getElementById('foto'), p = document.getElementById('prev'), ic = document.getElementById('prevIcon');
  f.onchange = function () {
    if (!f.files[0]) return;
    p.src = URL.createObjectURL(f.files[0]); p.hidden = false; if (ic) ic.hidden = true;
  };
})();
</script>
@endsection
