@extends('layouts.app')
@section('title', 'Dokumen & Unduhan - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Dokumen & Unduhan'])
<main class="wrap py-10">
  <div class="mb-5 flex flex-wrap gap-2 text-xs font-semibold">
    <a href="{{ route('dokumen') }}" class="rounded-full px-4 py-1.5 {{ ! $kat ? 'bg-padi text-white' : 'bg-padi-50 text-padi' }}">Semua</a>
    @foreach ($kats as $k)<a href="{{ route('dokumen', ['kategori' => $k]) }}" class="rounded-full px-4 py-1.5 {{ $kat === $k ? 'bg-padi text-white' : 'bg-padi-50 text-padi' }}">{{ $k }}</a>@endforeach
  </div>
  <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
    <table class="w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="p-3">Judul</th><th class="p-3">Kategori</th><th class="p-3">Tahun</th><th class="p-3"></th></tr></thead>
      @forelse ($docs as $d)
        <tr class="border-t border-slate-100"><td class="p-3 font-semibold">{{ $d->judul }}</td><td class="p-3">{{ $d->kategori }}</td><td class="p-3">{{ $d->tahun }}</td>
          <td class="p-3 text-right"><a href="{{ asset('storage/'.$d->file) }}" download class="rounded-lg bg-padi px-3 py-1.5 text-xs font-bold text-white hover:bg-padi-700"><i class="bi bi-download"></i> Unduh</a></td></tr>
      @empty<tr><td colspan="4" class="p-6 text-center text-slate-400">Belum ada dokumen.</td></tr>@endforelse
    </table>
  </div>
  <div class="mt-6">{{ $docs->links() }}</div>
</main>
@endsection
