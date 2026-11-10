@extends('admin.layout')
@section('title', $u->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')
@section('content')
@php
  $peranOpsi = config('akses.peran');
  $grup = collect(config('akses.menu'))->map(fn ($m, $k) => $m + ['key' => $k])->groupBy('grup');
  $pilih = old('menus', $terpilih);
  $role = old('role', $u->role);
  $fc = fn ($n) => 'form-control'.($errors->has($n) ? ' is-invalid' : '');
@endphp
<form method="post" action="{{ $u->exists ? route('admin.users.update', $u) : route('admin.users.store') }}" autocomplete="off">
  @csrf @if($u->exists) @method('PUT') @endif
  <div class="row">
    <div class="col-lg-5">
      <div class="card"><div class="card-header"><h3 class="card-title">Akun</h3></div>
        <div class="card-body">
          <div class="form-group"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $u->name) }}" class="{{ $fc('name') }}"></div>
          <div class="form-group"><label for="email">Email (untuk login)</label><input id="email" type="email" name="email" value="{{ old('email', $u->email) }}" class="{{ $fc('email') }}"></div>
          <div class="form-group"><label for="password">{{ $u->exists ? 'Password baru (kosongkan bila tidak diubah)' : 'Password (min. 10 karakter)' }}</label><input id="password" type="password" name="password" class="{{ $fc('password') }}" autocomplete="new-password"></div>
          <div class="form-group"><label for="role">Peran</label>
            <select id="role" name="role" class="{{ $fc('role') }}">@foreach($peranOpsi as $k => $v)<option value="{{ $k }}" @selected($role === $k)>{{ $v }}</option>@endforeach</select></div>
          <div class="form-group" id="rowUnit"><label for="unit_id">Bidang / UPTD</label>
            <select id="unit_id" name="unit_id" class="{{ $fc('unit_id') }}"><option value="">— pilih —</option>
              @foreach(['bidang' => 'Bidang', 'uptd' => 'UPTD'] as $t => $tl)<optgroup label="{{ $tl }}">@foreach($units->where('tipe', $t) as $x)<option value="{{ $x->id }}" @selected((string) old('unit_id', $u->unit_id) === (string) $x->id)>{{ $x->nama }}</option>@endforeach</optgroup>@endforeach
            </select>
            <small class="text-muted">Menentukan dokumen SIMBADOK bidang yang bisa dilihat dan diubah akun ini.</small></div>
        </div>
      </div>
      <div class="card card-outline card-info"><div class="card-body small">
        <b>Aturan hak akses</b>
        <ul class="mb-0 pl-3 mt-1">
          <li><b>Superadmin</b>: semua menu dan semua dokumen, termasuk membuat akun dan kategori SIMBADOK.</li>
          <li><b>Admin Bidang/UPTD</b>: menambah, mengubah, dan menghapus pada menu yang dicentang.</li>
          <li><b>Operator</b>: menambah dan mengubah, tidak bisa menghapus.</li>
          <li><b>SIMBADOK</b>: admin dan operator hanya mengelola dokumen bidang/UPTD-nya sendiri; dokumen berakses publik atau internal milik bidang lain hanya bisa dilihat.</li>
        </ul>
      </div></div>
    </div>
    <div class="col-lg-7">
      <div id="infoSuper" class="alert alert-info" hidden>Superadmin otomatis memiliki akses ke semua menu, jadi pilihan menu tidak diperlukan.</div>
      <div class="card" id="boxMenu">
        <div class="card-header"><h3 class="card-title">Menu yang boleh diakses</h3>
          <div class="card-tools"><button type="button" class="btn btn-xs btn-outline-primary" id="pAll">Pilih semua</button> <button type="button" class="btn btn-xs btn-outline-secondary" id="pNone">Kosongkan</button> <button type="button" class="btn btn-xs btn-outline-success" id="pDef">Isi default peran</button></div></div>
        <div class="card-body">
          @if($errors->has('menus') || $errors->has('menus.*'))<div class="text-danger small mb-2">{{ $errors->first('menus') ?: $errors->first('menus.*') }}</div>@endif
          @foreach (config('akses.grup') as $g)
            @if(isset($grup[$g]))
              <h6 class="text-muted text-uppercase mt-2">{{ $g }}</h6>
              <div class="row">
                @foreach ($grup[$g] as $m)
                  <div class="col-md-6"><div class="custom-control custom-checkbox mb-1">
                    <input type="checkbox" class="custom-control-input mk" id="m_{{ $m['key'] }}" name="menus[]" value="{{ $m['key'] }}" @checked(in_array($m['key'], (array) $pilih))>
                    <label class="custom-control-label font-weight-normal" for="m_{{ $m['key'] }}"><i class="fas {{ $m['icon'] }} text-muted"></i> {{ $m['label'] }}</label>
                  </div></div>
                @endforeach
              </div>
            @endif
          @endforeach
          <p class="small text-muted mt-3 mb-0">Hanya menu yang dicentang yang tampil di dashboard dan sidebar akun ini. Menu pengelolaan pengguna, kategori SIMBADOK, pengaturan situs, dan log hanya untuk superadmin.</p>
        </div>
      </div>
    </div>
  </div>
  <button class="btn btn-primary">Simpan</button> <a class="btn btn-default" href="{{ route('admin.users.index') }}">Batal</a>
</form>
<script>
(function () {
  var PRESET = @json(config('akses.preset'));
  var role = document.getElementById('role'), cek = document.querySelectorAll('.mk');
  function sync() {
    var su = role.value === 'superadmin';
    document.getElementById('boxMenu').hidden = su; document.getElementById('infoSuper').hidden = !su; document.getElementById('rowUnit').hidden = su;
  }
  function set(f) { [].forEach.call(cek, function (c) { c.checked = f(c.value); }); }
  role.onchange = sync; sync();
  document.getElementById('pAll').onclick = function () { set(function () { return true; }); };
  document.getElementById('pNone').onclick = function () { set(function () { return false; }); };
  document.getElementById('pDef').onclick = function () { var p = PRESET[role.value] || []; set(function (v) { return p.indexOf(v) > -1; }); };
})();
</script>
@endsection
