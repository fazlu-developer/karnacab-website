<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NestApiClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
    ) {}

    public static function make(): self
    {
        return new self(
            (string) config('karnacab.api.base_url'),
            (int) config('karnacab.api.timeout'),
        );
    }

    public function health(): array
    {
        return $this->request('get', '/health')->json() ?? [];
    }

    public function cmsSite(): array
    {
        $response = $this->request('get', '/cms/site');
        $response->throw();

        return $response->json() ?? [];
    }

    public function createLead(array $payload): array
    {
        $response = $this->request('post', '/leads', $payload);
        $response->throw();

        return $response->json() ?? [];
    }

    public function quoteRide(array $payload): array
    {
        $response = $this->request('post', '/quotes/ride', $payload);
        $response->throw();

        return $response->json() ?? [];
    }

    public function registerDriver(array $payload): array
    {
        $response = $this->request('post', '/auth/register-driver', $payload);
        $response->throw();

        return $response->json() ?? [];
    }

    public function registerAdvertiser(array $payload): array
    {
        $response = $this->request('post', '/auth/register-advertiser', $payload);
        $response->throw();

        return $response->json() ?? [];
    }

    public function request(string $method, string $path, array $payload = []): Response
    {
        $url = $this->baseUrl.'/'.ltrim($path, '/');

        $pending = Http::acceptJson()
            ->timeout($this->timeout)
            ->connectTimeout(5)
            ->retry(1, 200);
        if (strtolower($method) === 'post') {
            return $pending->asJson()->post($url, $payload);
        }

        return $pending->{$method}($url, $payload);
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }
}
