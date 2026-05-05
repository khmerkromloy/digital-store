<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_en(): void
    {
        $this->get('/')->assertOk();
        $this->assertSame('en', app()->getLocale());
    }

    public function test_setting_km_via_route_returns_no_content(): void
    {
        $response = $this->post('/locale/km');
        $response->assertNoContent();
        $cookie = $response->getCookie('locale');
        $this->assertNotNull($cookie);
        $this->assertSame('km', $cookie->getValue());
    }

    public function test_unsupported_locale_404s(): void
    {
        $this->post('/locale/fr')->assertNotFound();
    }

    public function test_locale_cookie_persists_across_requests(): void
    {
        $this->withCookie('locale', 'km')
            ->get('/')
            ->assertOk();

        $this->assertSame('km', app()->getLocale());
    }
}
