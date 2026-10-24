<?php

namespace App\Services;

final class AppRelease
{
    /**
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    public static function customer(): ?array
    {
        return self::find('customer', 'apk', [
            'KarnaRide-user.apk',
            'karnaride-customer-latest.apk',
            'KarnaRide-Customer-latest.apk',
        ], 'KarnaRide-Customer-latest.apk');
    }

    /**
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    public static function customerAab(): ?array
    {
        return self::find('customer', 'aab', [], 'KarnaRide-Customer-latest.aab');
    }

    /**
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    public static function driver(): ?array
    {
        return self::find('driver', 'apk', [
            'KarnaRide-driver.apk',
            'karnaride-partner-latest.apk',
            'KarnaRide-Partner-latest.apk',
        ], 'KarnaRide-Partner-latest.apk');
    }

    /**
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    public static function driverAab(): ?array
    {
        return self::find('driver', 'aab', [], 'KarnaRide-Partner-latest.aab');
    }

    /**
     * @param  list<string>  $preferred
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    private static function find(string $audience, string $extension, array $preferred, string $downloadName): ?array
    {
        $candidates = [];
        $extension = strtolower($extension);
        foreach (self::directories() as $dir) {
            foreach ($preferred as $name) {
                $path = $dir.DIRECTORY_SEPARATOR.$name;
                if (is_file($path)) {
                    $candidates[] = $path;
                }
            }
            $patterns = $audience === 'driver'
                ? ['*Partner*.'.$extension, '*Driver*.'.$extension]
                : ['*Customer*.'.$extension];
            foreach ($patterns as $pattern) {
                foreach (glob($dir.DIRECTORY_SEPARATOR.$pattern) ?: [] as $path) {
                    if (is_file($path)) {
                        $candidates[] = $path;
                    }
                }
            }
        }
        $candidates = array_values(array_unique($candidates));
        if ($candidates === []) {
            return null;
        }
        $published = array_values(array_filter($candidates, fn (string $path) => filesize($path) > 1024));
        $pool = $published !== [] ? $published : $candidates;
        usort($pool, fn (string $a, string $b) => filemtime($b) <=> filemtime($a));

        return self::meta($pool[0], $downloadName);
    }

    /**
     * @return list<string>
     */
    private static function directories(): array
    {
        return array_values(array_filter([
            storage_path('app/apps'),
            public_path('apps'),
            dirname(base_path()).DIRECTORY_SEPARATOR.'releases',
        ], fn ($dir) => is_dir($dir)));
    }

    /**
     * @return array{path: string, filename: string, bytes: int, version: string}
     */
    private static function meta(string $path, string $downloadName): array
    {
        $base = basename($path);
        preg_match('/(\d+\.\d+\.\d+)/', $base, $match);

        return [
            'path' => $path,
            'filename' => $downloadName,
            'bytes' => (int) filesize($path),
            'version' => $match[1] ?? 'latest',
        ];
    }
}
