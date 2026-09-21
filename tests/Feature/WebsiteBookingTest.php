<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebsiteBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['karnacab.google_maps_key' => 'test-maps-key']);
        Http::fake([
            'https://maps.googleapis.com/maps/api/place/autocomplete/json*' => Http::response([
                'status' => 'OK',
                'predictions' => [[
                    'place_id' => 'ChIJdrop',
                    'description' => 'Gandhi Maidan, Patna',
                    'structured_formatting' => [
                        'main_text' => 'Gandhi Maidan',
                        'secondary_text' => 'Patna',
                    ],
                ]],
            ], 200),
            'https://maps.googleapis.com/maps/api/place/details/json*' => Http::response([
                'status' => 'OK',
                'result' => [
                    'name' => 'Gandhi Maidan',
                    'formatted_address' => 'Gandhi Maidan, Patna',
                    'geometry' => ['location' => ['lat' => 25.61, 'lng' => 85.14]],
                ],
            ], 200),
            'https://maps.googleapis.com/maps/api/directions/json*' => Http::response([
                'status' => 'OK',
                'routes' => [[
                    'legs' => [[
                        'distance' => ['value' => 4200],
                        'duration' => ['value' => 720],
                    ]],
                    'overview_polyline' => ['points' => 'abc'],
                ]],
            ], 200),
            '*/cms/site' => Http::response(['pages' => []], 200),
            'http://127.0.0.1:8001/api/v1/rides/catalog' => Http::response([
                'products' => [['key' => 'LOCAL_CAB', 'title' => 'Local Cab']],
                'rideTypes' => [['key' => 'LOCAL_CAB', 'title' => 'Local Cab']],
                'vehicles' => [['key' => 'SEDAN', 'title' => 'Sedan']],
                'vehicleTypes' => [['key' => 'SEDAN', 'title' => 'Sedan']],
                'rentalHours' => [8],
            ], 200),
            'http://127.0.0.1:8001/api/v1/rides/quote' => Http::response([
                'source' => 'server',
                'totalPaise' => 15120,
                'billedKm' => 10,
            ], 200),
            'http://127.0.0.1:8001/api/v1/places/directions' => Http::response([
                'distanceKm' => 4.2,
                'durationSeconds' => 720,
            ], 200),
        ]);
    }

    public function test_book_page_checks_fares_for_each_vehicle(): void
    {
        $this->get('/book')->assertOk()->assertSee('data-place-search', false)->assertSee('Check fares');
        $this->getJson('/places/suggest?q=Patna')->assertOk()->assertJsonPath('predictions.0.placeId', 'ChIJdrop');

        $this->post('/book/quote', [
            'product' => 'LOCAL_CAB',
            'pickupText' => 'Patna Junction',
            'dropText' => 'Gandhi Maidan',
            'pickupLat' => 25.6,
            'pickupLng' => 85.1,
            'dropLat' => 25.61,
            'dropLng' => 85.14,
        ])->assertRedirect();

        $this->get('/book')
            ->assertOk()
            ->assertSee('Sedan')
            ->assertSee('Bike')
            ->assertSee('₹151');
    }

    public function test_proceed_to_book_asks_to_download_the_app(): void
    {
        $this->post('/book/continue', ['category' => 'SEDAN'])
            ->assertRedirect(route('download', ['category' => 'SEDAN']));

        $this->get('/download')
            ->assertOk()
            ->assertSee('Please download')
            ->assertSee('Customer app')
            ->assertSee('Driver app')
            ->assertSee('Download customer app');
    }

    public function test_route_check_lists_vehicle_prices(): void
    {
        $this->get('/route?pickup=Patna+Junction&drop=Gandhi+Maidan&pickup_lat=25.6&pickup_lng=85.1&drop_lat=25.61&drop_lng=85.14')
            ->assertOk()
            ->assertSee('Vehicles and fares')
            ->assertSee('Proceed to book');
    }
}
