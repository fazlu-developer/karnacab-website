<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '*/cms/site' => Http::response(['pages' => []], 200),
            '*/leads' => Http::response(['id' => '1', 'status' => 'NEW', 'type' => 'SUPPORT'], 200),
            'http://127.0.0.1:8001/api/v1/*' => Http::response([
                'products' => [['key' => 'LOCAL_CAB', 'title' => 'Local Cab']],
                'vehicles' => [['key' => 'SEDAN', 'title' => 'Sedan']],
                'rentalHours' => [8],
                'predictions' => [['placeId' => 'abc', 'title' => 'Patna', 'address' => 'Patna Junction']],
                'source' => 'server',
                'totalPaise' => 15120,
            ], 200),
        ]);
    }

    public function test_public_pages_render(): void
    {
        $pages = [
            '/',
            '/rides',
            '/route',
            '/about',
            '/how-it-works',
            '/safety',
            '/drive',
            '/support',
            '/privacy',
            '/terms',
            '/contact',
            '/corporate',
            '/business',
            '/book',
            '/franchise',
            '/local-cab',
            '/one-way',
            '/round-way',
            '/rental',
            '/schedule',
            '/outstation',
            '/airport',
            '/railway',
            '/parcel',
            '/bihar-parcel',
            '/travel',
            '/bulk-booking',
            '/fleet-partner',
            '/advertise',
            '/login',
            '/register',
            '/sitemap.xml',
            '/robots.txt',
        ];

        foreach ($pages as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_home_includes_pickup_drop_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('name="pickup"', false)
            ->assertSee('name="drop"', false)
            ->assertSee('data-place-search', false)
            ->assertSee('Get route');
    }

    public function test_contact_form_accepts_a_message(): void
    {
        $this->post('/contact', [
            'name' => 'Rakesh',
            'phone' => '9876543210',
            'email' => 'rakesh@example.com',
            'message' => 'Need help with the route page.',
        ])->assertRedirect();
    }

    public function test_faq_redirects_to_support(): void
    {
        $this->get('/faq')->assertRedirect('/support');
    }

    public function test_product_pages_include_request_form(): void
    {
        $this->get('/one-way')
            ->assertOk()
            ->assertSee('name="type"', false)
            ->assertSee('RIDE');
    }
}
