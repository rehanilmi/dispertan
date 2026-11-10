@extends('layouts.app')
@section('title', 'Kelompok Tani (SIMLUHTAN) - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Kelompok Tani (SIMLUHTAN)'])
@php $in = 'rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-padi focus:outline-none'; @endphp
<main class="wrap py-10">
  <div class="mb-6 grid gap-4 md:grid-cols-3">
    @foreach([['Kelompok tani terdaftar', $stat['total']], ['Total anggota', $stat['anggota']], ['Desa/kelurahan', $stat['desa']]] as [$l, $v])
      <div class="rounded-2xl bg-gradient-to-br from-[#0B2A6B] via-[#1F4FBF] to-[#4F7BE8] p-5 text-white shadow-lg"><div class="text-3xl font-extrabold">{{ number_format($v, 0, ',', '.') }}</div><div class="text-xs opacity-80">{{ $l }}</div></div>
    @endforeach
  </div>
  <form method="get" class="mb-5 flex flex-wrap gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
    <input name="q" value="{{ request('q') }}" placeholder="Cari nama poktan, ID, desa, atau ketua…" aria-label="Cari" class="{{ $in }} min-w-[240px] flex-1">
    @if($kecamatans->isNotEmpty())<select name="kecamatan" aria-label="Kecamatan" class="{{ $in }}"><option value="">Semua kecamatan</option>@foreach($kecamatans as $k)<option @selected(request('kecamatan') === $k)>{{ $k }}</option>@endforeach</select>@endif
    <button class="rounded-lg bg-padi px-5 py-2 text-sm font-bold text-white hover:bg-padi-700">Cari</button>
  </form>
  <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
    <table class="w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="p-3">No</th><th class="p-3">Nama Poktan</th><th class="p-3">ID Poktan</th><th class="p-3 text-right">Anggota</th><th class="p-3">Desa</th><th class="p-3">Ketua</th><th class="p-3">Alamat Sekretariat</th></tr></thead>
      @forelse($items as $k)
        <tr class="border-t border-slate-100 hover:bg-padi-50/50"><td class="p-3 text-slate-400">{{ $items->firstItem() + $loop->index }}</td><td class="p-3 font-semibold">{{ $k->nama }}</td><td class="p-3">{{ $k->id_poktan }}</td>
          <td class="p-3 text-right">{{ $k->jumlah_anggota }}</td><td class="p-3">{{ $k->desa }}</td><td class="p-3">{{ $k->ketua }}</td><td class="p-3 text-xs text-slate-500">{{ $k->alamat_sekretariat }}</td></tr>
      @empty<tr><td colspan="7" class="p-8 text-center text-slate-400">Data kelompok tani tidak ditemukan.</td></tr>@endforelse
    </table>
  </div>
  <div class="mt-6">{{ $items->links() }}</div>
  <p class="mt-4 text-xs text-slate-400">Data bersumber dari SIMLUHTAN Kementerian Pertanian dan diperbarui oleh Dinas Pertanian Kota Padang. Data pribadi anggota tidak ditampilkan untuk umum.</p>
</main>
@endsection
