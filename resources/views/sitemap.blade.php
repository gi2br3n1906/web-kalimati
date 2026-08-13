{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($staticUrls as $url)
    <url>
        <loc>{{ $url['location'] }}</loc>
        <priority>{{ $url['priority'] }}</priority>
    </url>
@endforeach
@foreach ($articles as $article)
    <url>
        <loc>{{ route('public.news.show', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at?->toAtomString() }}</lastmod>
        <priority>0.7</priority>
    </url>
@endforeach
</urlset>