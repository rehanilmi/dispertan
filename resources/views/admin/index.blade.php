@extends('admin.layout')
@section('title', $c['title'])
@section('content')
<div class="card">
  <div class="card-header">@unless($c['no_create'] ?? false)<a class="btn btn-primary btn-sm" href="{{ route('admin.create', $res) }}"><i class="fas fa-plus"></i> Tambah</a>@endunless</div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped">
      <thead><tr>@foreach ($c['columns'] as $col)<th>{{ $c['fields'][$col]['label'] }}</th>@endforeach<th style="width:110px"></th></tr></thead>
      <tbody>
      @forelse ($rows as $row)
        <tr>
          @foreach ($c['columns'] as $col)
            @php $v = $row->$col; @endphp
            <td>@if(($c['fields'][$col]['type'] ?? '') === 'image')@if($v)<img src="{{ asset('storage/'.$v) }}" alt="" style="height:44px;max-width:90px;object-fit:cover;border-radius:6px">@else<span class="text-muted">bawaan</span>@endif @else{{ $v instanceof \Carbon\Carbon ? $v->format('d-m-Y') : \Illuminate\Support\Str::limit((string) ($c['fields'][$col]['options'][(string) $v] ?? $v), 60) }}@endif</td>
          @endforeach
          <td class="text-nowrap">
            @unless($c['no_edit'] ?? false)<a class="btn btn-sm btn-warning" href="{{ route('admin.edit', [$res, $row->id]) }}"><i class="fas fa-edit"></i></a>@endunless
            @unless(($c['no_delete'] ?? false) || ! \App\Support\Akses::bisaHapus(auth()->user()))<form class="d-inline" method="post" action="{{ route('admin.destroy', [$res, $row->id]) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')
              <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>@endunless
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ count($c['columns']) + 1 }}" class="text-center text-muted">Belum ada data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $rows->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
