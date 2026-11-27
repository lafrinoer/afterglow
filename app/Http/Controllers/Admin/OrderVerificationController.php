<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class OrderVerificationController extends Controller
{
    public function proof(Order $order)
    {
        abort_unless($order->payment_proof_path, 404);

        return Storage::disk('local')->response($order->payment_proof_path, null, ['Content-Disposition' => 'inline']);
    }

    public function approve(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->status === 'waiting_verification' && $lockedOrder->payment_proof_path, 422);
            $lockedOrder->update(['status' => 'verified', 'verified_by' => auth()->id(), 'verified_at' => now(), 'admin_note' => null]);
            $tierIds = $lockedOrder->day_pass
                ? $lockedOrder->ticketTier->newQuery()->where('zone_name', $lockedOrder->ticketTier->zone_name)->orderBy('festival_day_id')->pluck('id')
                : collect([$lockedOrder->ticket_tier_id]);
            for ($index = 0; $index < $lockedOrder->quantity; $index++) {
                foreach ($tierIds as $tierId) {
                    $ticket = Ticket::create([
                        'order_id' => $lockedOrder->id, 'user_id' => $lockedOrder->user_id,
                        'ticket_tier_id' => $tierId, 'ticket_code' => (string) Str::uuid(), 'qr_payload' => 'pending',
                    ]);
                    $ticket->update(['qr_payload' => URL::temporarySignedRoute('tickets.validate', now()->addMonths(6), ['ticket' => $ticket->id])]);
                }
            }
        });

        return back()->with('success', 'Pembayaran disetujui. E-tiket telah diterbitkan.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['required', 'string', 'max:1000']]);
        DB::transaction(function () use ($data, $order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->status === 'waiting_verification', 422);
            $lockedOrder->update(['status' => 'rejected', 'admin_note' => $data['admin_note'], 'verified_by' => auth()->id(), 'verified_at' => now(), 'expires_at' => now()->addDay()]);
        });

        return back()->with('success', 'Pembayaran ditolak. Peserta dapat mengunggah bukti pembayaran yang benar.');
    }
}
