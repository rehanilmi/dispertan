@extends('admin.layout')
@section('title', 'Pengaturan Situs')
@section('content')
<div class="card"><form method="post" enctype="multipart/form-data" action="{{ route('admin.setting.update') }}">@csrf
  <div class="card-body">
    @foreach ($fields as $k => $f)
      <div class="form-group">
        <label for="{{ $k }}">{{ $f['label'] }}</label>
        @if($f['type'] === 'image')
          @if(\App\Models\Setting::get($k))<div class="mb-2"><img src="{{ asset('storage/'.\App\Models\Setting::get($k)) }}" style="max-height:70px" alt=""></div>@endif
          <input type="file" id="{{ $k }}" name="{{ $k }}" accept="image/*" class="d-block">
        @else
          <input id="{{ $k }}" name="{{ $k }}" value="{{ old($k, \App\Models\Setting::get($k)) }}" class="form-control @error($k) is-invalid @enderror">
        @endif
        @error($k)<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
    @endforeach
  </div>
  <div class="card-footer"><button class="btn btn-primary">Simpan</button></div>
</form></div>
@endsection
