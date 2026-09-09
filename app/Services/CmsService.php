<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Throwable;

class CmsService
{
    public function __construct(private readonly NestApiClient $api) {}

    public function site(): array
    {
        return Cache::remember('karnacab.cms.site', 60, function () {
            try {
                $payload = $this->api->cmsSite();
                if (! empty($payload['pages'])) {
                    return $this->normalize($payload);
                }
            } catch (Throwable) {
                // Render fallback copy when Nest is down.
            }

            return $this->fallback();
        });
    }

    public function page(string $slug): ?array
    {
        $site = $this->site();
        foreach ($site['pages'] as $page) {
            if (($page['slug'] ?? '') === $slug) {
                return $page;
            }
        }

        return null;
    }

    public function home(): array
    {
        return $this->page('home') ?? [
            'slug' => 'home',
            'title' => 'KarnaCab',
            'lede' => 'Rides, parcel and travel in Bihar.',
            'template' => 'home',
            'body' => ['sections' => []],
        ];
    }

    private function normalize(array $payload): array
    {
        $payload['site'] = is_array($payload['site'] ?? null) ? $payload['site'] : [];
        $payload['pages'] = is_array($payload['pages'] ?? null) ? $payload['pages'] : [];
        $payload['nav'] = is_array($payload['nav'] ?? null) ? $payload['nav'] : [];
        $payload['catalog'] = is_array($payload['catalog'] ?? null) ? $payload['catalog'] : [];
        $payload['faqs'] = is_array($payload['faqs'] ?? null) ? $payload['faqs'] : [];
        $payload['promo'] = is_array($payload['promo'] ?? null) ? $payload['promo'] : [];

        return $payload;
    }

    private function fallback(): array
    {
        $pages = [];
        foreach (config('karnacab_pages') as $slug => $row) {
            $pages[] = [
                'slug' => $slug,
                'path' => $slug === 'home' ? '/' : '/'.$slug,
                'title' => $row['title'],
                'eyebrow' => $row['eyebrow'] ?? '',
                'seoTitle' => ($row['title'] ?? 'KarnaCab').' | KarnaCab',
                'seoDescription' => $row['lede'] ?? '',
                'lede' => $row['lede'] ?? '',
                'template' => $row['template'] ?? 'service',
                'leadType' => $row['lead_type'] ?? null,
                'registerKind' => $row['register_kind'] ?? null,
                'productKey' => $row['product_key'] ?? null,
                'navGroup' => $row['nav_group'] ?? 'rides',
                'navLabel' => $row['nav_label'] ?? $row['title'],
                'body' => ['sections' => []],
            ];
        }

        return $this->normalize([
            'site' => [
                'name' => 'KarnaCab',
                'tagline' => 'Bike, auto, and cab across Bihar',
                'footerBlurb' => 'Rides, parcel, travel, bulk and corporate. Live catalog loads from the KarnaCab API.',
                'contactEmail' => '',
                'contactPhone' => '',
                'canonicalHost' => '',
                'defaultSeoTitle' => 'KarnaCab',
                'defaultSeoDescription' => 'KarnaCab public website',
                'ogImage' => '',
            ],
            'promo' => [],
            'pages' => $pages,
            'nav' => [],
            'catalog' => [
                'rideTypes' => [],
                'vehicleTypes' => [],
                'districts' => [],
                'packages' => [],
                'rentalPackages' => [],
                'rideServices' => [],
            ],
            'faqs' => [],
        ]);
    }
}
