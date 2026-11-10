@extends('layouts.app')
@section('title', 'Bidang dan UPTD - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => 'Bidang dan UPTD'])
<main class="wrap space-y-12 py-10">
  @foreach ([['Bidang', $bidang, true], ['Unit Pelaksana Teknis Dinas (UPTD)', $uptd, false]] as [$judul, $list, $bd])
    <section>
      <h2 class="mb-5 border-l-4 {{ $bd ? 'border-padi' : 'border-langit' }} pl-3 font-display text-2xl font-bold">{{ $judul }}</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($list as $u)
          <a href="{{ $u->url }}" class="group flex flex-col gap-2 rounded-2xl border border-slate-100 border-l-4 bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.05)] transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_20px_35px_rgba(2,132,199,0.12)] {{ $bd ? 'border-l-padi' : 'border-l-langit' }}">
            <span class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-xl text-xl {{ $bd ? 'bg-padi-100 text-padi-700' : 'bg-langit-50 text-langit' }}"><i data-lucide="{{ $bd ? 'network' : 'building-2' }}" class="h-6 w-6"></i></span><b class="font-display text-[17px] leading-snug">{{ $u->nama }}</b></span>
            @if($u->ringkasan)<span class="line-clamp-3 text-sm leading-relaxed text-slate-500">{{ $u->ringkasan }}</span>@endif
          </a>
        @empty<p class="text-slate-400">Belum ada data.</p>@endforelse
      </div>
    </section>
  @endforeach
</main>
@endsection
