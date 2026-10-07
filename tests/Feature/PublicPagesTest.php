<?php

namespace Tests\Feature;

use App\Mail\WebsiteLeadMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '*/cms/site' => Http::response(['pages' => []], 200),
            '*/leads' => Http::response(['id' => '1', 'status' => 'NEW', 'type' => 'SUPPORT'], 200),
            '*/auth/otp/request' => Http::response(['ok' => true, 'expiresInSeconds' => 300], 200),
            '*/auth/account/delete' => Http::response(['ok' => true, 'deleted' => true], 200),
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
            '/privacy-policy',
            '/delete-account',
            '/terms',
            '/return-refund',
            '/software-license',
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
            '/cities',
            '/operators',
            '/download',
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
            ->assertSee('Get route')
            ->assertSee('Auto')
            ->assertSee('Cab')
            ->assertSee('What is KarnaRide')
            ->assertSee('Customer app')
            ->assertSee('Driver app')
            ->assertDontSee('Log in')
            ->assertDontSee('Karna Cab')
            ->assertDontSee("The use statement with non-compound name 'Throwable'");
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

    public function test_contact_emails_ops_when_api_is_down(): void
    {
        Mail::fake();
        config(['karnacab.leads_notify_email' => 'ops@karnacab.test']);
        $this->mock(\App\Services\NestApiClient::class, function ($mock) {
            $mock->shouldReceive('createLead')->once()->andThrow(new \RuntimeException('API down'));
        });

        $this->post('/contact', [
            'name' => 'Rakesh',
            'phone' => '9876543210',
            'email' => 'rakesh@example.com',
            'message' => 'Need help with the route page.',
        ])->assertRedirect();

        Mail::assertSent(WebsiteLeadMail::class);
    }

    public function test_login_and_register_redirect_to_home(): void
    {
        $this->get('/login')->assertRedirect('/');
        $this->get('/register')->assertRedirect('/');
    }

    public function test_about_explains_karnacab(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('What is KarnaRide')
            ->assertSee('app-first taxi')
            ->assertSee('KARNACAB TRANSPORT SERVICE PRIVATE LIMITED');
    }

    public function test_privacy_policy_renders_copy(): void
    {
        $this->get('/privacy')
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('do not sell personal data')
            ->assertSee('precise location')
            ->assertSee('LOCATION DATA WE ACCESS');
        $this->get('/privacy-policy')
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('LOCATION DATA WE ACCESS');
        $this->get('/delete-account')
            ->assertOk()
            ->assertSee('Delete Account')
            ->assertSee('Permanently delete my account');
        $this->get('/terms')
            ->assertOk()
            ->assertSee('Terms of Use')
            ->assertSee('Saharsa');
        $this->get('/return-refund')
            ->assertOk()
            ->assertSee('Cancellation')
            ->assertSee('2–3 business days');
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
