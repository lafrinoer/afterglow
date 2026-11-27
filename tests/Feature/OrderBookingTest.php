<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\TicketTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'test@example.com')->firstOrFail());
    }

    public function test_booking_uses_current_database_price_and_reserves_stock(): void
    {
        $tier = TicketTier::whereHas('festivalDay', fn ($query) => $query->where('day_number', 1))
            ->where('zone_name', 'Regular')->firstOrFail();

        $this->post(route('orders.store'), ['ticket_tier_id' => $tier->id, 'quantity' => 2])
            ->assertRedirect(route('dashboard'));

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame(240000 + $order->unique_code, $order->total_amount);
        $this->assertSame(2, $tier->fresh()->sold_count);
    }

    public function test_three_day_pass_sums_all_current_tier_prices_and_reserves_each_day(): void
    {
        $tier = TicketTier::whereHas('festivalDay', fn ($query) => $query->where('day_number', 1))
            ->where('zone_name', 'Regular')->firstOrFail();

        $this->post(route('orders.store'), ['ticket_tier_id' => $tier->id, 'quantity' => 1, 'day_pass' => 1])
            ->assertRedirect(route('dashboard'));

        $order = Order::latest('id')->firstOrFail();
        $this->assertTrue($order->day_pass);
        $this->assertSame(520000 + $order->unique_code, $order->total_amount);
        $this->assertSame([1, 1, 1], TicketTier::orderBy('festival_day_id')->where('zone_name', 'Regular')->pluck('sold_count')->all());
    }

    public function test_three_day_pass_is_rejected_without_partial_reservation_when_a_day_is_sold_out(): void
    {
        $tiers = TicketTier::where('zone_name', 'Regular')->orderBy('festival_day_id')->get();
        $tiers[1]->update(['sold_count' => $tiers[1]->quota]);

        $this->post(route('orders.store'), ['ticket_tier_id' => $tiers[0]->id, 'quantity' => 1, 'day_pass' => 1])
            ->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(0, $tiers[0]->fresh()->sold_count);
        $this->assertSame($tiers[1]->quota, $tiers[1]->fresh()->sold_count);
        $this->assertSame(0, $tiers[2]->fresh()->sold_count);
    }
}
