<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentAndTicketLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_payment_verification_qr_and_pdf_work_after_support_removal(): void
    {
        $this->seed();
        $this->travelTo(Carbon::parse('2026-12-29 15:00:00'));
        Storage::fake('local');
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $admin = User::where('email', 'admin@festival.com')->firstOrFail();
        $tier = TicketTier::whereHas('festivalDay', fn ($query) => $query->where('day_number', 1))
            ->where('zone_name', 'Regular')->firstOrFail();

        $this->actingAs($user)->post(route('orders.store'), [
            'ticket_tier_id' => $tier->id,
            'quantity' => 1,
        ])->assertRedirect(route('dashboard'));

        $order = Order::latest('id')->firstOrFail();
        $this->post(route('orders.proof', $order), [
            'payment_proof' => UploadedFile::fake()->createWithContent('receipt.pdf', "%PDF-1.4\nAFTERGLOW payment proof\n"),
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame('waiting_verification', $order->status);
        Storage::disk('local')->assertExists($order->payment_proof_path);

        $this->actingAs($admin)->post(route('admin.orders.approve', $order))->assertRedirect();

        $order->refresh();
        $ticket = Ticket::where('order_id', $order->id)->firstOrFail();
        $this->assertSame('verified', $order->status);
        $this->assertSame(1, Ticket::where('order_id', $order->id)->count());
        $this->assertStringContainsString('signature=', $ticket->qr_payload);

        $this->actingAs($user)->get(route('orders.download', $order))
            ->assertOk()
            ->assertDownload($order->order_code.'.pdf')
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($admin)->get(route('admin.checkin'))->assertOk();
        $this->post(route('admin.checkin.store'), ['ticket_code' => $ticket->ticket_code])
            ->assertRedirect();
        $this->assertTrue($ticket->fresh()->is_checked_in);
    }
}
