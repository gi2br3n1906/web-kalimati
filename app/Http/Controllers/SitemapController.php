<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $articles = NewsArticle::query()
            ->published()
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get();

        return response()
            ->view('sitemap', [
                'staticUrls' => [
                    ['location' => route('public.home'), 'priority' => '1.0'],
                    ['location' => route('public.gis.map'), 'priority' => '0.9'],
                    ['location' => route('public.news.index'), 'priority' => '0.8'],
                    ['location' => route('public.gps.sync'), 'priority' => '0.5'],
                ],
                'articles' => $articles,
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
