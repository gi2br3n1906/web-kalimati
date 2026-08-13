<?php

declare(strict_types=1);

use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Support\Carbon;

it('serves a dynamic XML sitemap with public pages and published news', function (): void {
    $updatedAt = Carbon::parse('2026-08-13 07:00:00', 'Asia/Jakarta');
    $publishedArticle = NewsArticle::factory()->published()->create([
        'slug' => 'kabar-publik-kalimati',
        'updated_at' => $updatedAt,
    ]);
    NewsArticle::factory()->create([
        'slug' => 'draft-tidak-boleh-terindeks',
        'is_published' => false,
        'published_at' => null,
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.route('public.home').'</loc>', escape: false)
        ->assertSee('<priority>1.0</priority>', escape: false)
        ->assertSee('<loc>'.route('public.gis.map').'</loc>', escape: false)
        ->assertSee('<priority>0.9</priority>', escape: false)
        ->assertSee('<loc>'.route('public.news.index').'</loc>', escape: false)
        ->assertSee('<priority>0.8</priority>', escape: false)
        ->assertSee('<loc>'.route('public.news.show', $publishedArticle->slug).'</loc>', escape: false)
        ->assertSee('<lastmod>'.$updatedAt->toAtomString().'</lastmod>', escape: false)
        ->assertSee('<priority>0.7</priority>', escape: false)
        ->assertSee('<loc>'.route('public.gps.sync').'</loc>', escape: false)
        ->assertSee('<priority>0.5</priority>', escape: false)
        ->assertDontSee('draft-tidak-boleh-terindeks');
});

it('serves robots directives for public search crawlers', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    expect(file_get_contents(public_path('robots.txt')))
        ->toContain('User-agent: *')
        ->toContain('Disallow: /admin/')
        ->toContain('Disallow: /api/')
        ->toContain('Sitemap: https://desakalimati.web.id/sitemap.xml');
});

it('renders dynamic SEO metadata for a public news article', function (): void {
    $author = User::factory()->create(['name' => 'Pemerintah Desa Kalimati']);
    $article = NewsArticle::factory()->published()->create([
        'author_id' => $author->getKey(),
        'title' => 'Panen Jagung Kalimati Meningkat',
        'slug' => 'panen-jagung-kalimati-meningkat',
        'content' => '<p>Kelompok tani memanfaatkan pemantauan sensor untuk menjaga kondisi lahan dan meningkatkan hasil panen jagung.</p>',
        'thumbnail_path' => 'https://cdn.example.com/panen-jagung.jpg',
    ]);

    $this->get(route('public.news.show', $article->slug))
        ->assertOk()
        ->assertSee('<title>Panen Jagung Kalimati Meningkat | Desa Kalimati</title>', escape: false)
        ->assertSee('<meta name="description" content="Kelompok tani memanfaatkan pemantauan sensor untuk menjaga kondisi lahan dan meningkatkan hasil panen jagung.">', escape: false)
        ->assertSee('<meta property="og:image" content="https://cdn.example.com/panen-jagung.jpg">', escape: false)
        ->assertSee('<meta property="og:type" content="article">', escape: false)
        ->assertSee('<link rel="canonical" href="'.route('public.news.show', $article->slug).'">', escape: false)
        ->assertSee('"@type":"GovernmentOffice"', escape: false)
        ->assertSee('"@type":"AdministrativeArea"', escape: false);
});
