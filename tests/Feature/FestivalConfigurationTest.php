<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FestivalConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_official_event_details_lineup_prices_and_contacts(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('End the year loud. Start anew together.')
            ->assertSee('Three days of pure emotion, midnight anthems, and the biggest year-end celebration in town.')
            ->assertSee('Kebun Raya Bogor')
            ->assertSee('29–31 Desember 2026')
            ->assertSee('Gerbang dibuka 14:00 WIB')
            ->assertSee('Semua usia')
            ->assertSee('Weird Genius')
            ->assertSee('00:20 WIB')
            ->assertSee('1 Januari 2027')
            ->assertSee('Rp120.000')
            ->assertSee('Rp1.500.000')
            ->assertSee('6282128943420')
            ->assertSee('afterglow@support.com')
            ->assertSee('wa.me/6282128943420')
            ->assertSee('Halo%20tim%20AFTERGLOW%21%20Saya%20ingin%20bertanya')
            ->assertSee('mailto:afterglow@support.com')
            ->assertDontSee('Kirim pesan');
    }

    public function test_internal_support_routes_are_removed(): void
    {
        $this->assertFalse(Route::has('support.store'));
        $this->assertFalse(Route::has('admin.support'));
    }
}
