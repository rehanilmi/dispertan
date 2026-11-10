@extends('admin.layout')
@section('title', ($row->exists ? 'Edit ' : 'Tambah ').$c['title'])
@section('content')
<div class="card"><form method="post" enctype="multipart/form-data"
  action="{{ $row->exists ? route('admin.update', [$res, $row->id]) : route('admin.store', $res) }}">
  @csrf @if($row->exists) @method('PUT') @endif
  <div class="card-body">
    @foreach ($c['fields'] as $k => $f)
      @php
        $t = $f['type']; $ro = ! empty($f['ro']);
        $val = old($k, $t === 'date' ? optional($row->$k)->format('Y-m-d') : ($t === 'password' ? '' : $row->$k));
        $cls = 'form-control'.($errors->has($k) ? ' is-invalid' : '');
      @endphp
      <div class="form-group">
        <label for="{{ $k }}">{{ $f['label'] }}</label>
        @switch($t)
          @case('textarea')<textarea id="{{ $k }}" name="{{ $k }}" rows="4" class="{{ $cls }}" @readonly($ro)>{{ $val }}</textarea>@break
          @case('richtext')<input type="hidden" id="x_{{ $k }}" name="{{ $k }}" value="{{ $val }}"><trix-editor input="x_{{ $k }}" class="bg-white"></trix-editor>@break
          @case('select')
            @php $opts = isset($f['source']) ? ['' => $f['blank'] ?? '—'] + call_user_func($f['source']) : $f['options']; @endphp
            <select id="{{ $k }}" name="{{ $k }}" class="{{ $cls }}">@foreach ($opts as $ov => $ol)<option value="{{ $ov }}" @selected((string) $val === (string) $ov)>{{ $ol }}</option>@endforeach</select>@break
          @case('image')
            @if($row->$k)<div class="mb-2"><img src="{{ asset('storage/'.$row->$k) }}" style="max-height:90px" alt=""></div>@endif
            <input type="file" id="{{ $k }}" name="{{ $k }}" accept="image/*" class="d-block">@break
          @case('file')
            @if($row->$k)<div class="mb-2"><a href="{{ asset('storage/'.$row->$k) }}" target="_blank">File saat ini</a></div>@endif
            <input type="file" id="{{ $k }}" name="{{ $k }}" class="d-block">@break
          @case('anggota')
            <table class="table table-sm"><tr><th>#</th><th>Nama</th><th>NIK</th><th>JK</th><th>Luas (ha)</th></tr>
            @forelse(($row->anggota ?? []) as $i => $a)<tr><td>{{ $i + 1 }}</td><td>{{ $a['nama'] ?? '' }}</td><td>{{ $a['nik'] ?? '' }}</td><td>{{ $a['jk'] ?? '' }}</td><td>{{ $a['luas'] ?? '' }}</td></tr>@empty<tr><td colspan="5" class="text-muted">Tidak ada data anggota.</td></tr>@endforelse</table>@break
          @default<input type="{{ $t === 'password' ? 'password' : $t }}" id="{{ $k }}" name="{{ $k }}" value="{{ $val }}" class="{{ $cls }}" autocomplete="off" @readonly($ro)>
        @endswitch
        @error($k)<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
    @endforeach
  </div>
  <div class="card-footer"><button class="btn btn-primary">Simpan</button> <a class="btn btn-default" href="{{ route('admin.index', $res) }}">Batal</a></div>
</form></div>
@endsection
