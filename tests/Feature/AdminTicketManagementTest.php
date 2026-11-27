<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\TicketTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTicketManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_current_price_without_changing_historical_order_total(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@festival.com')->firstOrFail();
        $tier = TicketTier::whereHas('festivalDay', fn ($query) => $query->where('day_number', 1))
            ->where('zone_name', 'Regular')->firstOrFail();
        $order = Order::create([
            'order_code' => 'FST-HISTORY1',
            'user_id' => User::where('email', 'test@example.com')->value('id'),
            'ticket_tier_id' => $tier->id,
            'quantity' => 1,
            'unique_code' => 123,
            'total_amount' => 120123,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Verifikasi Pembayaran')
            ->assertSee('Kuota dan Harga per Hari')
            ->assertDontSee('Pesan Pelanggan Terbuka')
            ->assertDontSee('admin.support');

        $this->actingAs($admin)->patch(route('admin.tiers.update', $tier), ['price' => 135000, 'quota' => $tier->quota])
            ->assertRedirect();

        $this->assertSame(135000, $tier->fresh()->price);
        $this->assertSame(120123, $order->fresh()->total_amount);
    }

    public function test_non_admin_cannot_change_ticket_prices(): void
    {
        $this->seed();
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $tier = TicketTier::firstOrFail();

        $this->actingAs($user)->patch(route('admin.tiers.update', $tier), ['price' => 1, 'quota' => $tier->quota])
            ->assertForbidden();

        $this->assertNotSame(1, $tier->fresh()->price);
    }
}
