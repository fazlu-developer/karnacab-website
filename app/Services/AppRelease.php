<?php

namespace App\Services;

final class AppRelease
{
    /**
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    public static function customer(): ?array
    {
        return self::find('customer', [
            'karnaride-customer-latest.apk',
            'KarnaRide-Customer-latest.apk',
            'KarnaRide-Customer-1.1.11-arm64.apk',
        ], 'KarnaRide-Customer-latest.apk');
    }

    /**
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    public static function driver(): ?array
    {
        return self::find('driver', [
            'karnaride-partner-latest.apk',
            'KarnaRide-Partner-latest.apk',
            'KarnaRide-Partner-1.1.11-arm64.apk',
        ], 'KarnaRide-Partner-latest.apk');
    }

    /**
     * @param  list<string>  $preferred
     * @return array{path: string, filename: string, bytes: int, version: string}|null
     */
    private static function find(string $audience, array $preferred, string $downloadName): ?array
    {
        foreach (self::directories() as $dir) {
            foreach ($preferred as $name) {
                $path = $dir.DIRECTORY_SEPARATOR.$name;
                if (is_file($path)) {
                    return self::meta($path, $downloadName);
                }
            }
            $pattern = $audience === 'driver'
                ? $dir.DIRECTORY_SEPARATOR.'*Partner*.apk'
                : $dir.DIRECTORY_SEPARATOR.'*Customer*.apk';
            $matches = glob($pattern) ?: [];
            usort($matches, fn ($a, $b) => filemtime($b) <=> filemtime($a));
            if ($matches !== []) {
                return self::meta($matches[0], $downloadName);
            }
        }

        return null;
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
