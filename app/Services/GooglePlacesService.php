<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GooglePlacesService
{
    /**
     * @return list<array{placeId: string, title: string, subtitle: string, address: string}>
     */
    public function autocomplete(string $query, ?string $types = null): array
    {
        if ($this->key() === '' || trim($query) === '') {
            return [];
        }
        $payload = $this->get('https://maps.googleapis.com/maps/api/place/autocomplete/json', array_filter([
            'input' => $query,
            'key' => $this->key(),
            'components' => 'country:in',
            'language' => 'en',
            'location' => '25.5941,85.1376',
            'radius' => '250000',
            'types' => $types,
        ], fn ($value) => $value !== null && $value !== ''));

        return array_map(function (array $row) {
            $fmt = is_array($row['structured_formatting'] ?? null) ? $row['structured_formatting'] : [];

            return [
                'placeId' => (string) ($row['place_id'] ?? ''),
                'title' => (string) ($fmt['main_text'] ?? $row['description'] ?? ''),
                'subtitle' => (string) ($fmt['secondary_text'] ?? ''),
                'address' => (string) ($row['description'] ?? ''),
            ];
        }, is_array($payload['predictions'] ?? null) ? $payload['predictions'] : []);
    }

    /**
     * @return array{placeId: string, title: string, address: string, lat: float, lng: float}
     */
    public function details(string $placeId): array
    {
        $payload = $this->get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'fields' => 'geometry,formatted_address,name',
            'key' => $this->key(),
        ]);
        $result = is_array($payload['result'] ?? null) ? $payload['result'] : [];
        $loc = is_array($result['geometry']['location'] ?? null) ? $result['geometry']['location'] : [];

        return [
            'placeId' => $placeId,
            'title' => (string) ($result['name'] ?? $result['formatted_address'] ?? ''),
            'address' => (string) ($result['formatted_address'] ?? $result['name'] ?? ''),
            'lat' => (float) ($loc['lat'] ?? 0),
            'lng' => (float) ($loc['lng'] ?? 0),
        ];
    }

    /**
     * @return array{distanceKm: float, durationMinutes: int, polyline: string}
     */
    public function directions(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        $payload = $this->get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $originLat.','.$originLng,
            'destination' => $destLat.','.$destLng,
            'mode' => 'driving',
            'key' => $this->key(),
        ]);
        $route = is_array($payload['routes'][0] ?? null) ? $payload['routes'][0] : [];
        $legs = is_array($route['legs'] ?? null) ? $route['legs'] : [];
        $meters = 0;
        $seconds = 0;
        foreach ($legs as $leg) {
            $meters += (int) ($leg['distance']['value'] ?? 0);
            $seconds += (int) ($leg['duration']['value'] ?? 0);
        }

        return [
            'distanceKm' => round($meters / 1000, 2),
            'durationMinutes' => max(1, (int) round($seconds / 60)),
            'polyline' => (string) ($route['overview_polyline']['points'] ?? ''),
        ];
    }

    private function key(): string
    {
        return (string) config('karnacab.google_maps_key');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function get(string $url, array $query): array
    {
        $response = Http::timeout((int) config('karnacab.maps.timeout', 8))->acceptJson()->get($url, $query);
        if (! $response->successful()) {
            throw new RuntimeException('Google Maps request failed');
        }
        $payload = is_array($response->json()) ? $response->json() : [];
        $status = (string) ($payload['status'] ?? '');
        if (! in_array($status, ['OK', 'ZERO_RESULTS'], true)) {
            throw new RuntimeException((string) ($payload['error_message'] ?? ('Google Maps '.$status)));
        }

        return $payload;
    }
}
