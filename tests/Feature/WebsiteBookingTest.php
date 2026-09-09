<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebsiteBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '*/cms/site' => Http::response(['pages' => []], 200),
            'http://127.0.0.1:8001/api/v1/places/autocomplete*' => Http::response([
                'predictions' => [[
                    'placeId' => 'ChIJdrop',
                    'title' => 'Gandhi Maidan',
                    'address' => 'Gandhi Maidan, Patna',
                ]],
            ], 200),
            'http://127.0.0.1:8001/api/v1/places/details*' => Http::response([
                'placeId' => 'ChIJdrop',
                'title' => 'Gandhi Maidan',
                'address' => 'Gandhi Maidan, Patna',
                'lat' => 25.61,
                'lng' => 85.14,
            ], 200),
            'http://127.0.0.1:8001/api/v1/rides/catalog' => Http::response([
                'products' => [['key' => 'LOCAL_CAB', 'title' => 'Local Cab']],
                'vehicles' => [['key' => 'SEDAN', 'title' => 'Sedan']],
                'rentalHours' => [8],
            ], 200),
            'http://127.0.0.1:8001/api/v1/rides/quote' => Http::response([
                'source' => 'server',
                'totalPaise' => 15120,
                'billedKm' => 10,
            ], 200),
            'http://127.0.0.1:8001/api/v1/rides/bookings' => Http::response([
                'id' => 44,
                'publicRef' => 'KCWEB1',
                'status' => 'REQUESTED',
                'quotePaise' => 15120,
                'pickupText' => 'Patna Junction',
                'dropText' => 'Gandhi Maidan',
                'passengerName' => 'Rakesh',
            ], 201),
            'http://127.0.0.1:8001/api/v1/rides/bookings/44/payments' => Http::response([
                'booking' => ['id' => 44, 'publicRef' => 'KCWEB1', 'status' => 'REQUESTED', 'quotePaise' => 15120],
                'payment' => ['id' => '9', 'status' => 'initiated', 'paymentReference' => 'KCP1', 'clientCaptureIgnored' => true],
            ], 201),
            'http://127.0.0.1:8001/api/v1/rides/bookings/44' => Http::response([
                'id' => 44,
                'publicRef' => 'KCWEB1',
                'status' => 'REQUESTED',
                'quotePaise' => 15120,
                'pickupText' => 'Patna Junction',
                'dropText' => 'Gandhi Maidan',
                'passengerName' => 'Rakesh',
            ], 200),
        ]);
    }

    public function test_book_page_and_google_place_proxy(): void
    {
        $this->get('/book')->assertOk()->assertSee('data-place-search', false)->assertSee('Get fare estimate');
        $this->getJson('/places/suggest?q=Patna')->assertOk()->assertJsonPath('predictions.0.placeId', 'ChIJdrop');
    }

    public function test_logged_in_customer_quotes_books_and_pays_via_laravel_api(): void
    {
        $user = User::factory()->create(['role' => 'CUSTOMER']);
        $this->actingAs($user)->post('/book/quote', [
            'product' => 'LOCAL_CAB',
            'category' => 'SEDAN',
            'pickupText' => 'Patna Junction',
            'dropText' => 'Gandhi Maidan',
            'pickupLat' => 25.6,
            'pickupLng' => 85.1,
            'dropLat' => 25.61,
            'dropLng' => 85.14,
        ])->assertRedirect();

        $this->actingAs($user)->post('/book', [
            'product' => 'LOCAL_CAB',
            'category' => 'SEDAN',
            'pickupText' => 'Patna Junction',
            'dropText' => 'Gandhi Maidan',
            'passengerName' => 'Rakesh',
            'passengerPhone' => '9876543210',
        ])->assertRedirect(route('book'));

        $this->actingAs($user)->post('/book/pay', [
            'method' => 'upi',
        ])->assertRedirect(route('book.confirm', ['id' => 44]));

        $this->actingAs($user)->get('/book/confirm/44')
            ->assertOk()
            ->assertSee('KCWEB1')
            ->assertSee('initiated');
    }
}
