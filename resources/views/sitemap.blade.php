{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  @foreach ([url('/'), route('berita.index'), route('dokumen'), route('galeri'), url('/kontak'), url('/pengaduan'), url('/ppid/permohonan')] as $u)<url><loc>{{ $u }}</loc></url>@endforeach
  @foreach ($berita as $b)<url><loc>{{ route('berita.show', $b->slug) }}</loc><lastmod>{{ $b->updated_at->toAtomString() }}</lastmod></url>@endforeach
  @foreach ($units as $u)<url><loc>{{ $u->url }}</loc></url>@endforeach
  @foreach ($pages as $p)<url><loc>{{ route('page', $p->slug) }}</loc></url>@endforeach
</urlset>
