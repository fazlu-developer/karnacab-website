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
        $cached = null;
        try {
            $cached = Cache::get('karnacab.cms.site');
        } catch (Throwable) {
            $cached = null;
        }
        if (is_array($cached) && ! empty($cached['catalog']['rideTypes']) && ! empty($cached['site']['logoUrl'])) {
            return $cached;
        }

        $payload = $this->platform->site()
            ?? $this->loadFromNest()
            ?? $this->loadFromLaravel()
            ?? $this->fallback();
        $payload = $this->normalize($payload);

        if (! empty($payload['catalog']['rideTypes'])) {
            try {
                Cache::put('karnacab.cms.site', $payload, 60);
            } catch (Throwable) {
            }
        }

        return $payload;
    }

    public function page(string $slug): ?array
    {
        $canonical = self::canonicalSlug($slug);
        $site = $this->site();
        $found = null;
        foreach ($site['pages'] as $page) {
            $pageSlug = (string) ($page['slug'] ?? '');
            if (self::canonicalSlug($pageSlug) !== $canonical && $pageSlug !== $slug) {
                continue;
            }
            if ($pageSlug === $canonical || $found === null) {
                $found = $page;
            }
            if ($pageSlug === $canonical) {
                break;
            }
        }
        $config = config('karnacab_pages.'.$canonical)
            ?? config('karnacab_pages.'.$slug)
            ?? config('karnacab_pages.'.self::configSlug($canonical));
        if (is_array($config)) {
            $cmsBody = $found['body'] ?? null;
            $configBody = $config['body'] ?? ['sections' => []];
            $found = array_merge($found ?? [
                'slug' => $canonical,
                'path' => '/'.$canonical,
                'template' => $config['template'] ?? 'legal',
            ], [
                'slug' => $canonical,
                'path' => $canonical === 'home' ? '/' : '/'.$canonical,
                'title' => $found['title'] ?? $config['title'] ?? $canonical,
                'eyebrow' => $found['eyebrow'] ?? $config['eyebrow'] ?? '',
                'lede' => $found['lede'] ?? $config['lede'] ?? '',
                'seoTitle' => $found['seoTitle'] ?? (($found['title'] ?? $config['title'] ?? 'KarnaRide').' | KarnaRide'),
                'seoDescription' => $found['seoDescription'] ?? ($found['lede'] ?? $config['lede'] ?? ''),
                'template' => $found['template'] ?? $config['template'] ?? 'legal',
                'body' => $this->bodyHasContent($cmsBody) ? $cmsBody : $configBody,
                'bodyHtml' => $found['bodyHtml'] ?? $this->bodyHtml($this->bodyHasContent($cmsBody) ? $cmsBody : $configBody),
                'leadType' => $found['leadType'] ?? $config['lead_type'] ?? null,
                'registerKind' => $found['registerKind'] ?? $config['register_kind'] ?? null,
                'productKey' => $found['productKey'] ?? $config['product_key'] ?? null,
            ]);
        } elseif ($found !== null) {
            $found['bodyHtml'] = $found['bodyHtml'] ?? $this->bodyHtml($found['body'] ?? null);
        }

        return $found;
    }

    public static function canonicalSlug(string $slug): string
    {
        return match ($slug) {
            'privacy', 'privacy-policy' => 'privacy-policy',
            'terms-conditions' => 'terms',
            'about-us' => 'about',
            default => $slug,
        };
    }

    public static function configSlug(string $slug): string
    {
        return match ($slug) {
            'privacy-policy' => 'privacy',
            default => $slug,
        };
    }

    private function bodyHasContent(mixed $body): bool
    {
        if (is_string($body)) {
            return trim($body) !== '';
        }
        if (! is_array($body)) {
            return false;
        }
        if (trim((string) ($body['html'] ?? $body['text'] ?? '')) !== '') {
            return true;
        }

        return ! empty($body['sections']);
    }

    private function bodyHtml(mixed $body): string
    {
        if (is_string($body)) {
            $decoded = json_decode($body, true);
            $body = is_array($decoded) ? $decoded : $body;
        }
        if (is_string($body)) {
            return $body;
        }
        if (! is_array($body)) {
            return '';
        }
        if (! empty($body['html']) || ! empty($body['text'])) {
            return (string) ($body['html'] ?? $body['text']);
        }
        $parts = [];
        foreach ($body['sections'] ?? [] as $section) {
            if (! is_array($section)) {
                continue;
            }
            if (! empty($section['heading'])) {
                $parts[] = $section['heading'];
            }
            if (! empty($section['text'])) {
                $parts[] = $section['text'];
            }
            foreach ($section['paragraphs'] ?? [] as $paragraph) {
                $parts[] = (string) $paragraph;
            }
        }

        return trim(implode("\n\n", array_filter($parts)));
    }

    public function home(): array
    {
        return $this->page('home') ?? [
            'slug' => 'home',
            'title' => 'KarnaRide',
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
            Log::warning('KarnaRide CMS Nest API failed: '.$error->getMessage());
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
            Log::warning('KarnaRide CMS Laravel API failed: '.$error->getMessage());
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
        $payload['offers'] = is_array($payload['offers'] ?? null) ? $payload['offers'] : [];
        foreach (['logoUrl', 'faviconUrl', 'ogImage', 'adminLogoUrl', 'customerAppLogoUrl', 'driverAppLogoUrl'] as $key) {
            $payload['site'][$key] = $this->publicAsset($payload['site'][$key] ?? '');
        }
        if ($payload['site']['logoUrl'] === '') {
            $payload['site']['logoUrl'] = asset('branding/karnacab-logo-full.png');
        }
        if ($payload['site']['faviconUrl'] === '') {
            $payload['site']['faviconUrl'] = asset('favicon-32.png');
        }
        if ($payload['site']['ogImage'] === '') {
            $payload['site']['ogImage'] = $payload['site']['logoUrl'];
        }

        $defaults = config('karnacab.default_catalog', []);
        if (empty($payload['catalog']['rideTypes'])) {
            $payload['catalog']['rideTypes'] = $defaults['rideTypes'] ?? [];
        }
        if (empty($payload['catalog']['vehicleTypes'])) {
            $payload['catalog']['vehicleTypes'] = $defaults['vehicleTypes'] ?? [];
        }

        return $payload;
    }

    private function publicAsset(mixed $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//')) {
            return $value;
        }
        $path = '/'.ltrim($value, '/');
        if (str_starts_with($path, '/uploads/')) {
            return rtrim((string) config('karnacab.admin_url'), '/').$path;
        }

        return asset(ltrim($path, '/'));
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
                'seoTitle' => ($row['title'] ?? 'KarnaRide').' | KarnaRide',
                'seoDescription' => $row['lede'] ?? '',
                'lede' => $row['lede'] ?? '',
                'template' => $row['template'] ?? 'service',
                'leadType' => $row['lead_type'] ?? null,
                'registerKind' => $row['register_kind'] ?? null,
                'productKey' => $row['product_key'] ?? null,
                'navGroup' => $row['nav_group'] ?? 'rides',
                'navLabel' => $row['nav_label'] ?? $row['title'],
                'body' => $row['body'] ?? ['sections' => []],
            ];
        }

        $defaults = config('karnacab.default_catalog', []);

        return [
            'site' => [
                'name' => 'KarnaRide',
                'tagline' => 'Bike, auto, and cab across Bihar',
                'footerBlurb' => 'Rides, parcel, travel, bulk and corporate.',
                'contactEmail' => '',
                'contactPhone' => '',
                'canonicalHost' => 'https://karnaride.in',
                'defaultSeoTitle' => 'KarnaRide — rides, parcel and travel',
                'defaultSeoDescription' => 'Book bike, auto, cab, parcel and travel with KarnaRide in Bihar and Delhi.',
                'contactEmail' => 'karnaride@gmail.com',
                'contactPhone' => '+91 1169 270 608',
                'faviconUrl' => asset('favicon-32.png'),
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
