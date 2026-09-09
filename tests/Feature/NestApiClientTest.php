<?php

namespace Tests\Feature;

use App\Services\NestApiClient;
use Tests\TestCase;

class NestApiClientTest extends TestCase
{
    public function test_api_client_uses_versioned_base_url(): void
    {
        $client = NestApiClient::make();

        $this->assertSame('http://localhost:3000/api/v1', $client->baseUrl());
    }

    public function test_laravel_api_client_points_at_management_admin(): void
    {
        $client = \App\Services\LaravelApiClient::make();

        $this->assertSame('http://127.0.0.1:8001/api/v1', $client->baseUrl());
    }
}
