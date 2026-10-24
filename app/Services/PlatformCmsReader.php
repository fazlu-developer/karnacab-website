<?php

namespace App\Services;

use App\Support\PublicMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PlatformCmsReader
{
    public function site(): ?array
    {
        try {
            $db = DB::connection('platform');
            if (! $this->hasTable($db, 'catalog_services') && ! $this->hasTable($db, 'cms_pages')) {
                return null;
            }

            $services = $this->hasTable($db, 'catalog_services')
                ? $db->table('catalog_services')->where('active', 1)->orderBy('sort_order')->get()
                : collect();
            $pages = $this->hasTable($db, 'cms_pages')
                ? $db->table('cms_pages')->where('published', 1)->orderBy('sort_order')->get()
                : collect();
            $settings = $this->hasTable($db, 'system_settings')
                ? $db->table('system_settings')->whereIn('key', [
                    'cms_site',
                    'cms_home_promo',
                    'driver_welcome_bonus_enabled',
                    'driver_welcome_bonus_rupees',
                    'customer_first_ride_free_enabled',
                ])->pluck('value', 'key')
                : collect();
            $districts = $this->hasTable($db, 'districts')
                ? $db->table('districts')->orderBy('name')->get(['id', 'name', 'code'])
                : collect();
            $packages = $this->hasTable($db, 'travel_packages')
                ? $db->table('travel_packages')->where('status', 'PUBLISHED')->orderBy('id')->get()
                : collect();
            $faqs = $this->hasTable($db, 'support_faqs')
                ? $db->table('support_faqs')->where('audience', 'customer')->where('active', 1)->orderBy('sort_order')->get()
                : collect();

            $ride = $services->where('service_group', 'RIDE')->values();
            $parcel = $services->where('service_group', 'PARCEL')->values();

            $presentedPages = $pages->map(fn ($row) => $this->presentPage($row))->all();
            $rideTypes = $this->rideTypes($presentedPages, $ride);
            $site = $this->presentSite($this->jsonSetting($settings['cms_site'] ?? null));

            return [
                'site' => $site,
                'banners' => $this->banners($db),
                'heads' => $this->heads($db),
                'coverage' => [
                    'states' => $this->hasTable($db, 'states') ? $db->table('states')->count() : 0,
                    'districts' => $districts->count(),
                ],
                'promo' => $this->jsonSetting($settings['cms_home_promo'] ?? null),
                'offers' => [
                    'customerFirstRideFreeEnabled' => $this->flagOn($settings['customer_first_ride_free_enabled'] ?? null),
                    'driverWelcomeBonusEnabled' => $this->flagOn($settings['driver_welcome_bonus_enabled'] ?? null),
                    'driverWelcomeBonusRupees' => is_numeric($settings['driver_welcome_bonus_rupees'] ?? null)
                        ? (int) $settings['driver_welcome_bonus_rupees']
                        : 100,
                ],
                'pages' => $presentedPages,
                'nav' => $this->navFrom($presentedPages),
                'catalog' => [
                    'rideServices' => $ride->map(fn ($row) => $this->presentService($row))->all(),
                    'parcelServices' => $parcel->map(fn ($row) => $this->presentService($row))->all(),
                    'services' => $services->map(fn ($row) => $this->presentService($row))->all(),
                    'rideTypes' => $rideTypes,
                    'vehicleTypes' => $this->vehicleTypes($db),
                    'districts' => $districts->map(fn ($row) => [
                        'id' => $row->id,
                        'name' => $row->name,
                        'code' => $row->code,
                    ])->all(),
                    'packages' => $packages->map(fn ($row) => [
                        'id' => $row->id,
                        'title' => $row->title,
                        'destination' => $row->destination,
                        'pricePaise' => $row->price_paise,
                    ])->all(),
                ],
                'faqs' => $faqs->map(fn ($row) => [
                    'question' => $row->question,
                    'answer' => $row->answer,
                ])->all(),
            ];
        } catch (Throwable $error) {
            Log::warning('KarnaRide platform DB CMS failed: '.$error->getMessage());

            return null;
        }
    }

    private function hasTable($db, string $table): bool
    {
        try {
            return $db->getSchemaBuilder()->hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    private function presentPage(object $row): array
    {
        $body = $row->body ?? null;
        if (is_string($body) && $body !== '') {
            $decoded = json_decode($body, true);
            $body = is_array($decoded) ? $decoded : ['sections' => []];
        }
        if (! is_array($body)) {
            $body = ['sections' => []];
        }
        $html = (string) ($body['html'] ?? $body['text'] ?? '');
        if ($html === '' && ! empty($body['sections']) && is_array($body['sections'])) {
            $chunks = [];
            foreach ($body['sections'] as $section) {
                if (! is_array($section)) {
                    continue;
                }
                $chunks[] = trim((string) ($section['heading'] ?? ''));
                $chunks[] = trim((string) ($section['text'] ?? ''));
                foreach ($section['paragraphs'] ?? [] as $paragraph) {
                    $chunks[] = trim((string) $paragraph);
                }
            }
            $html = trim(implode("\n\n", array_filter($chunks)));
        }

        return [
            'id' => (string) $row->id,
            'slug' => $row->slug,
            'path' => $row->slug === 'home' ? '/' : '/'.$row->slug,
            'title' => $row->title,
            'eyebrow' => $row->eyebrow,
            'seoTitle' => $row->seo_title ?: $row->title.' | KarnaRide',
            'seoDescription' => $row->seo_description ?: $row->lede,
            'lede' => $row->lede,
            'body' => $body,
            'bodyHtml' => $html,
            'template' => $row->template,
            'leadType' => $row->lead_type,
            'registerKind' => $row->register_kind,
            'productKey' => $row->product_key,
            'navGroup' => $row->nav_group,
            'navLabel' => $row->nav_label,
            'sortOrder' => (int) $row->sort_order,
            'published' => (bool) $row->published,
        ];
    }

    private function presentService(object $row): array
    {
        $vehicles = $this->col($row, 'vehicle_categories');
        if (is_string($vehicles)) {
            $decoded = json_decode($vehicles, true);
            $vehicles = is_array($decoded) ? $decoded : [];
        }
        $key = strtoupper((string) ($this->col($row, 'category_key') ?? ''));
        if ($key === '') {
            $key = strtoupper(str_replace('-', '_', (string) $row->slug));
        }

        return [
            'id' => $row->id,
            'slug' => $row->slug,
            'title' => $row->title,
            'subtitle' => $row->subtitle,
            'group' => $row->service_group,
            'key' => $key,
            'imageUrl' => PublicMedia::presentMediaUrl($this->col($row, 'image_url'), $this->col($row, 'updated_at')),
            'vehicles' => is_array($vehicles) ? array_values($vehicles) : [],
        ];
    }

    /**
     * @param  array<string, mixed>  $site
     * @return array<string, mixed>
     */
    private function presentSite(array $site): array
    {
        foreach (['logoUrl', 'faviconUrl', 'ogImage', 'customerAppLogoUrl', 'driverAppLogoUrl'] as $key) {
            if (! empty($site[$key])) {
                $site[$key] = PublicMedia::presentMediaUrl($site[$key]) ?? '';
            }
        }

        return $site;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function banners($db): array
    {
        if (! $this->hasTable($db, 'ad_campaigns')) {
            return [];
        }
        $rows = $db->table('ad_campaigns')
            ->whereIn('status', ['published', 'active', 'PUBLISHED', 'ACTIVE'])
            ->orderByDesc('id')
            ->limit(6)
            ->get();
        $out = [];
        foreach ($rows as $row) {
            $url = PublicMedia::presentMediaUrl($this->col($row, 'image_url') ?: $this->col($row, 'banner_key'), $this->col($row, 'updated_at'));
            if ($url === null) {
                continue;
            }
            $out[] = [
                'title' => $this->col($row, 'title') ?: ($this->col($row, 'name') ?: 'KarnaRide'),
                'imageUrl' => $url,
                'href' => $this->col($row, 'click_url') ?: ($this->col($row, 'cta_url') ?: $this->col($row, 'target_url')),
            ];
        }

        return $out;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function heads($db): array
    {
        if (! $this->hasTable($db, 'head_info')) {
            return [];
        }
        $rows = $db->table('head_info')->orderBy('role')->limit(4)->get();
        $out = [];
        foreach ($rows as $row) {
            $phone = (string) ($this->col($row, 'support_phone') ?? '');
            $email = (string) ($this->col($row, 'email') ?? '');
            if (trim($phone) === '' && trim($email) === '') {
                continue;
            }
            $out[] = [
                'role' => $this->col($row, 'role'),
                'phone' => $phone,
                'email' => $email,
                'address' => $this->col($row, 'address'),
            ];
        }

        return $out;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function vehicleTypes($db): array
    {
        $fallback = config('karnacab.default_catalog.vehicleTypes', []);
        if (! $this->hasTable($db, 'catalog_vehicle_types')) {
            return $fallback;
        }
        $rows = $db->table('catalog_vehicle_types')->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return $fallback;
        }
        $out = [];
        foreach ($rows as $row) {
            if ($this->col($row, 'active') !== null && ! $this->col($row, 'active')) {
                continue;
            }
            $out[] = [
                'key' => $this->col($row, 'category_key') ?: ($this->col($row, 'key') ?: $this->col($row, 'slug')),
                'title' => $this->col($row, 'label') ?: $this->col($row, 'title') ?: $this->col($row, 'name') ?: $this->col($row, 'family_label'),
                'imageUrl' => PublicMedia::presentMediaUrl($this->col($row, 'image_url') ?: $this->col($row, 'family_image_url'), $this->col($row, 'updated_at')),
            ];
        }

        return $out !== [] ? $out : $fallback;
    }

    private function rideTypes(array $pages, $rideServices): array
    {
        $fromServices = $rideServices->map(fn ($row) => [
            'key' => strtoupper(str_replace('-', '_', (string) $row->slug)),
            'title' => $row->title,
        ])->values()->all();
        if ($fromServices !== []) {
            return $fromServices;
        }

        $fromPages = [];
        foreach ($pages as $page) {
            if (! empty($page['productKey'])) {
                $fromPages[] = [
                    'key' => $page['productKey'],
                    'title' => $page['navLabel'] ?: $page['title'],
                ];
            }
        }
        if ($fromPages !== []) {
            return $fromPages;
        }

        return [];
    }

    private function navFrom(array $pages): array
    {
        $groups = ['primary', 'rides', 'services', 'company', 'legal'];

        $appOnly = ['about-us'];

        return array_map(function (string $group) use ($pages, $appOnly) {
            $items = array_values(array_filter($pages, function ($page) use ($group, $appOnly) {
                $slug = (string) ($page['slug'] ?? '');

                return ($page['navGroup'] ?? '') === $group
                    && $slug !== 'home'
                    && ! in_array($slug, $appOnly, true);
            }));
            usort($items, fn ($a, $b) => ($a['sortOrder'] ?? 0) <=> ($b['sortOrder'] ?? 0));
            $seen = [];
            $unique = [];
            foreach ($items as $page) {
                $canonical = CmsService::canonicalSlug((string) $page['slug']);
                $label = strtolower(trim((string) ($page['navLabel'] ?: $page['title'])));
                $key = $canonical.'|'.$label;
                if (isset($seen[$key]) || isset($seen[$canonical])) {
                    continue;
                }
                $seen[$key] = true;
                $seen[$canonical] = true;
                $unique[] = $page;
            }

            return [
                'group' => $group,
                'items' => array_map(function ($page) {
                    $canonical = CmsService::canonicalSlug((string) $page['slug']);

                    return [
                        'slug' => $canonical,
                        'path' => $canonical === 'home' ? '/' : '/'.$canonical,
                        'label' => CmsService::publicNavLabel($canonical, (string) ($page['title'] ?: $page['navLabel'])),
                    ];
                }, $unique),
            ];
        }, $groups);
    }

    private function jsonSetting(?string $raw): array
    {
        if (! $raw) {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function col(object $row, string $name): mixed
    {
        return property_exists($row, $name) ? $row->{$name} : null;
    }

    private function flagOn(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}
