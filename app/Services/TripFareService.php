<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Throwable;

class TripFareService
{
    public function __construct(private readonly LaravelApiClient $laravel) {}

    /**
     * @param  array<string, mixed>  $trip
     * @return array{distanceKm: ?float, vehicles: list<array<string, mixed>>}
     */
    public function quotes(array $trip): array
    {
        $distanceKm = isset($trip['distanceKm']) ? (float) $trip['distanceKm'] : null;
        if ($distanceKm === null || $distanceKm <= 0) {
            $distanceKm = $this->distanceFromCoords($trip);
        }
        $product = strtoupper((string) ($trip['product'] ?? 'LOCAL_CAB'));
        $vehicles = [];
        foreach (config('karnacab.default_catalog.vehicleTypes', []) as $row) {
            $key = strtoupper((string) ($row['key'] ?? ''));
            if ($key === '') {
                continue;
            }
            $item = [
                'key' => $key,
                'title' => (string) ($row['title'] ?? $key),
                'totalPaise' => null,
                'billedKm' => $distanceKm,
            ];
            try {
                $quote = $this->laravel->quoteRide([
                    'product' => $product,
                    'category' => $key,
                    'distanceKm' => $distanceKm && $distanceKm > 0 ? $distanceKm : 5,
                    'pickupLat' => $trip['pickupLat'] ?? $trip['pickup_lat'] ?? null,
                    'pickupLng' => $trip['pickupLng'] ?? $trip['pickup_lng'] ?? null,
                    'dropLat' => $trip['dropLat'] ?? $trip['drop_lat'] ?? null,
                    'dropLng' => $trip['dropLng'] ?? $trip['drop_lng'] ?? null,
                    'pickupText' => $trip['pickupText'] ?? $trip['pickup'] ?? null,
                    'dropText' => $trip['dropText'] ?? $trip['drop'] ?? null,
                ]);
                $item['totalPaise'] = isset($quote['totalPaise']) ? (int) $quote['totalPaise'] : null;
                if (! empty($quote['billedKm'])) {
                    $item['billedKm'] = (float) $quote['billedKm'];
                }
            } catch (Throwable $exception) {
                $message = $exception->getMessage();
                if ($exception instanceof RequestException) {
                    $message = (string) ($exception->response->json('message') ?: $message);
                }
                $lower = strtolower($message);
                if (str_contains($lower, 'coming soon') || str_contains($lower, 'not live')) {
                    return [
                        'distanceKm' => $distanceKm,
                        'vehicles' => [],
                        'comingSoon' => true,
                        'message' => $message,
                    ];
                }
            }
            $vehicles[] = $item;
        }

        return [
            'distanceKm' => $distanceKm,
            'vehicles' => $vehicles,
            'comingSoon' => false,
            'message' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $trip
     */
    private function distanceFromCoords(array $trip): ?float
    {
        $oLat = $trip['pickupLat'] ?? $trip['pickup_lat'] ?? null;
        $oLng = $trip['pickupLng'] ?? $trip['pickup_lng'] ?? null;
        $dLat = $trip['dropLat'] ?? $trip['drop_lat'] ?? null;
        $dLng = $trip['dropLng'] ?? $trip['drop_lng'] ?? null;
        if ($oLat === null || $oLng === null || $dLat === null || $dLng === null || $oLat === '' || $dLat === '') {
            return null;
        }
        try {
            $route = $this->laravel->directions((float) $oLat, (float) $oLng, (float) $dLat, (float) $dLng);

            return isset($route['distanceKm']) ? (float) $route['distanceKm'] : null;
        } catch (Throwable) {
            return null;
        }
    }
}
