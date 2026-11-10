@extends('layouts.app')
@section('title', 'Info Harga Komoditas - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Info Harga Komoditas'])
<main class="wrap py-10">
  <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
    <table class="w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs text-slate-500"><tr><th class="p-3">Komoditas</th><th class="p-3">Harga</th><th class="p-3">Perubahan</th><th class="p-3">Pasar</th><th class="p-3">Tanggal</th></tr></thead>
      @forelse($rows as $nama => $r)
        @php $sel = $r['prev'] ? $r['now']->harga - $r['prev']->harga : 0; @endphp
        <tr class="border-t border-slate-100"><td class="p-3 font-semibold">{{ $nama }}</td>
          <td class="p-3">Rp {{ number_format($r['now']->harga, 0, ',', '.') }} / {{ $r['now']->satuan }}</td>
          <td class="p-3 font-semibold {{ $sel > 0 ? 'text-red-600' : ($sel < 0 ? 'text-padi' : 'text-slate-400') }}">{!! $sel > 0 ? '&#9650; +' : ($sel < 0 ? '&#9660; ' : '&ndash; ') !!}{{ $sel ? number_format($sel, 0, ',', '.') : 'tetap' }}</td>
          <td class="p-3 text-slate-500">{{ $r['now']->pasar }}</td><td class="p-3 text-slate-500">{{ $r['now']->tanggal->locale(app()->getLocale())->translatedFormat('d F Y') }}</td></tr>
      @empty<tr><td colspan="5" class="p-8 text-center text-slate-400">Belum ada data harga.</td></tr>@endforelse
    </table>
  </div>
  <p class="mt-3 text-xs text-slate-400">Perubahan dibandingkan data sebelumnya (14 hari terakhir).</p>
</main>
@endsection
