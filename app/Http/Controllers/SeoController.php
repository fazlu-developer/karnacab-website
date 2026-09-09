<?php

namespace App\Http\Controllers;

use App\Services\CmsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(CmsService $cms): Response
    {
        $site = $cms->site();
        $host = rtrim((string) ($site['site']['canonicalHost'] ?? ''), '/');
        if ($host === '') {
            $host = rtrim(config('app.url'), '/');
        }

        $urls = [['loc' => $host.'/', 'priority' => '1.0']];
        foreach ($site['pages'] as $page) {
            $slug = $page['slug'] ?? '';
            if ($slug === '' || $slug === 'home') {
                continue;
            }
            $urls[] = [
                'loc' => $host.'/'.$slug,
                'priority' => '0.8',
            ];
        }
        $urls[] = ['loc' => $host.'/route', 'priority' => '0.6'];

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(Request $request): Response
    {
        $sitemap = $request->getSchemeAndHttpHost().'/sitemap.xml';
        $body = "User-agent: *\nAllow: /\nDisallow: /dashboard\nDisallow: /login\nSitemap: {$sitemap}\n";

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
