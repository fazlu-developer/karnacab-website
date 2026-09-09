<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class LaravelApiClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly string $bookingSecret,
    ) {}

    public static function make(): self
    {
        return new self(
            (string) config('karnacab.laravel.base_url'),
            (int) config('karnacab.laravel.timeout'),
            (string) config('karnacab.laravel.booking_secret'),
        );
    }

    public function health(): array
    {
        return $this->request('get', '/rides/catalog')->json() ?? [];
    }

    public function rideCatalog(): array
    {
        $response = $this->request('get', '/rides/catalog');
        $response->throw();

        return $response->json() ?? [];
    }

    public function quoteRide(array $payload): array
    {
        $response = $this->request('post', '/rides/quote', $payload);
        $response->throw();

        return $response->json() ?? [];
    }

    public function placeAutocomplete(string $query, ?string $types = null): array
    {
        $response = $this->request('get', '/places/autocomplete', array_filter([
            'q' => $query,
            'types' => $types,
        ]));
        $response->throw();

        return $response->json() ?? [];
    }

    public function placeDetails(string $placeId): array
    {
        $response = $this->request('get', '/places/details', ['placeId' => $placeId]);
        $response->throw();

        return $response->json() ?? [];
    }

    public function directions(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        $response = $this->request('post', '/places/directions', [
            'originLat' => $originLat,
            'originLng' => $originLng,
            'destLat' => $destLat,
            'destLng' => $destLng,
        ]);
        $response->throw();

        return $response->json() ?? [];
    }

    public function createBooking(array $payload, array $customer): array
    {
        $response = $this->signed('post', '/rides/bookings', $payload, $customer);
        $response->throw();

        return $response->json() ?? [];
    }

    public function showBooking(int $id, array $customer): array
    {
        $response = $this->signed('get', '/rides/bookings/'.$id, [], $customer);
        $response->throw();

        return $response->json() ?? [];
    }

    public function payBooking(int $id, array $payload, array $customer): array
    {
        $response = $this->signed('post', '/rides/bookings/'.$id.'/payments', $payload, $customer);
        $response->throw();

        return $response->json() ?? [];
    }

    public function request(string $method, string $path, array $payload = []): Response
    {
        $url = $this->baseUrl.'/'.ltrim($path, '/');
        $pending = Http::acceptJson()->timeout($this->timeout);
        if (in_array(strtolower($method), ['post', 'patch', 'put'], true)) {
            return $pending->asJson()->{strtolower($method)}($url, $payload);
        }

        return $pending->{strtolower($method)}($url, $payload);
    }

    public function signed(string $method, string $path, array $payload, array $customer): Response
    {
        $url = $this->baseUrl.'/'.ltrim($path, '/');
        $urlPath = (string) parse_url($url, PHP_URL_PATH);
        $email = strtolower(trim((string) ($customer['email'] ?? '')));
        $body = in_array(strtolower($method), ['post', 'patch', 'put'], true) ? json_encode($payload) : '';
        $ts = (string) time();
        $canonical = $ts."\n".strtoupper($method)."\n".$urlPath."\n".$body."\n".$email;
        $sig = hash_hmac('sha256', $canonical, $this->bookingSecret);
        $pending = Http::acceptJson()->timeout($this->timeout)->withHeaders([
            'X-Karnacab-Website-Timestamp' => $ts,
            'X-Karnacab-Website-Signature' => $sig,
            'X-Karnacab-Customer-Email' => $email,
            'X-Karnacab-Customer-Name' => (string) ($customer['name'] ?? 'Customer'),
            'X-Karnacab-Customer-Phone' => (string) ($customer['phone'] ?? ''),
        ]);
        if (in_array(strtolower($method), ['post', 'patch', 'put'], true)) {
            return $pending->asJson()->{strtolower($method)}($url, $payload);
        }

        return $pending->{strtolower($method)}($url);
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }
}
