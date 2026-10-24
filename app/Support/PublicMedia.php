<?php

namespace App\Support;

/**
 * Uploaded files are served from the admin host. Marketing and API hosts are not image CDNs.
 */
final class PublicMedia
{
    public const ORIGIN = 'https://admin.karnaride.in';

    public static function presentMediaUrl(mixed $path, mixed $version = null): ?string
    {
        $relative = self::storagePath($path);
        if ($relative !== null) {
            $url = self::ORIGIN.$relative;
            $stamp = trim((string) $version);
            if ($stamp !== '') {
                $url .= '?v='.rawurlencode($stamp);
            }

            return $url;
        }

        $raw = trim((string) $path);
        if (! str_starts_with(strtolower($raw), 'https://')) {
            return null;
        }
        $host = strtolower((string) parse_url($raw, PHP_URL_HOST));
        $onlyPath = (string) parse_url($raw, PHP_URL_PATH);
        $blocked = ['karnaride.in', 'www.karnaride.in', 'api.karnaride.in', 'localhost', '127.0.0.1', '10.0.2.2', '0.0.0.0'];
        if ($host === '' || in_array($host, $blocked, true) || str_starts_with($onlyPath, '/storage') || str_contains($onlyPath, '/api/')) {
            return null;
        }

        return $raw;
    }

    public static function storagePath(mixed $path): ?string
    {
        $raw = trim((string) $path);
        if ($raw === '' || strcasecmp($raw, 'null') === 0) {
            return null;
        }
        $parts = parse_url($raw);
        $onlyPath = is_array($parts) && isset($parts['path']) ? (string) $parts['path'] : $raw;
        $onlyPath = '/'.ltrim(str_replace('\\', '/', $onlyPath), '/');
        if ($onlyPath === '/' || str_contains($onlyPath, '..') || preg_match('#^/storage(/|$)#', $onlyPath)) {
            return null;
        }
        if (! str_starts_with($onlyPath, '/uploads/')) {
            return null;
        }

        return $onlyPath;
    }
}
