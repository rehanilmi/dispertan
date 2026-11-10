@extends('layouts.app')
@section('title', $p->judul.' - Dinas Pertanian Kota Padang')
@section('content')
@include('partials.phead', ['t' => $p->judul])
<main class="wrap py-10"><div class="prose prose-slate max-w-none rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">{!! $p->konten !!}</div></main>
@endsection
