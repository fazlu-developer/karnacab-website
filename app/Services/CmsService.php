<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class CmsService
{
    public function __construct(
        private readonly NestApiClient $api,
        private readonly LaravelApiClient $laravel,
        private readonly PlatformCmsReader $platform,
    ) {}

    public function site(): array
    {
        $cached = Cache::get('karnacab.cms.site');
        if (is_array($cached) && ! empty($cached['catalog']['rideTypes'])) {
            return $cached;
        }

        $payload = $this->platform->site()
            ?? $this->loadFromNest()
            ?? $this->loadFromLaravel()
            ?? $this->fallback();
        $payload = $this->normalize($payload);

        if (! empty($payload['catalog']['rideTypes'])) {
            Cache::put('karnacab.cms.site', $payload, 60);
        }

        return $payload;
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

    private function loadFromNest(): ?array
    {
        try {
            $payload = $this->api->cmsSite();
            if (! empty($payload['pages']) || ! empty($payload['catalog']['rideTypes'])) {
                return $payload;
            }
        } catch (Throwable $error) {
            Log::warning('KarnaCab CMS Nest API failed: '.$error->getMessage());
        }

        return null;
    }

    private function loadFromLaravel(): ?array
    {
        try {
            $catalog = $this->laravel->rideCatalog();
            if ($catalog === []) {
                return null;
            }

            $fallback = $this->fallback();
            $fallback['catalog'] = array_merge($fallback['catalog'], [
                'rideTypes' => $catalog['rideTypes'] ?? $fallback['catalog']['rideTypes'],
                'vehicleTypes' => $catalog['vehicleTypes'] ?? $fallback['catalog']['vehicleTypes'],
                'packages' => $catalog['packages'] ?? [],
                'rideServices' => $catalog['rideServices'] ?? [],
                'districts' => $catalog['districts'] ?? [],
            ]);

            return $fallback;
        } catch (Throwable $error) {
            Log::warning('KarnaCab CMS Laravel API failed: '.$error->getMessage());
        }

        return null;
    }

    private function normalize(array $payload): array
    {
        $payload['site'] = is_array($payload['site'] ?? null) ? $payload['site'] : [];
        $payload['pages'] = is_array($payload['pages'] ?? null) ? $payload['pages'] : [];
        $payload['nav'] = is_array($payload['nav'] ?? null) ? $payload['nav'] : [];
        $payload['catalog'] = is_array($payload['catalog'] ?? null) ? $payload['catalog'] : [];
        $payload['faqs'] = is_array($payload['faqs'] ?? null) ? $payload['faqs'] : [];
        $payload['promo'] = is_array($payload['promo'] ?? null) ? $payload['promo'] : [];

        $defaults = config('karnacab.default_catalog', []);
        if (empty($payload['catalog']['rideTypes'])) {
            $payload['catalog']['rideTypes'] = $defaults['rideTypes'] ?? [];
        }
        if (empty($payload['catalog']['vehicleTypes'])) {
            $payload['catalog']['vehicleTypes'] = $defaults['vehicleTypes'] ?? [];
        }

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

        $defaults = config('karnacab.default_catalog', []);

        return [
            'site' => [
                'name' => 'KarnaCab',
                'tagline' => 'Bike, auto, and cab across Bihar',
                'footerBlurb' => 'Rides, parcel, travel, bulk and corporate.',
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
                'rideTypes' => $defaults['rideTypes'] ?? [],
                'vehicleTypes' => $defaults['vehicleTypes'] ?? [],
                'districts' => [],
                'packages' => [],
                'rentalPackages' => [],
                'rideServices' => [],
            ],
            'faqs' => [],
        ];
    }
}
