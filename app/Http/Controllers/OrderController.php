<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StorePaymentProofRequest;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketTier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function dashboard(): View
    {
        $orders = auth()->user()->orders()->with('ticketTier.festivalDay')->latest()->get();
        $tickets = auth()->user()->tickets()->with('ticketTier.festivalDay')->latest()->get();
        $tiers = TicketTier::query()->with('festivalDay')->orderBy('festival_day_id')->get();

        return view('dashboard', compact('orders', 'tickets', 'tiers'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $order = DB::transaction(function () use ($data, $request): Order {
            $selectedTier = TicketTier::query()->findOrFail($data['ticket_tier_id']);
            $dayPass = (bool) ($data['day_pass'] ?? false);
            $tiers = $dayPass
                ? TicketTier::query()->where('zone_name', $selectedTier->zone_name)->orderBy('festival_day_id')->lockForUpdate()->get()
                : collect([TicketTier::query()->lockForUpdate()->findOrFail($selectedTier->id)]);
            abort_if($dayPass && $tiers->count() !== 3, 422, 'Tiket Pass 3 Hari belum tersedia.');
            foreach ($tiers as $tier) {
                abort_if($tier->sold_count + $data['quantity'] > $tier->quota, 422, 'Kuota pada salah satu hari sudah tidak mencukupi.');
            }
            $code = random_int(100, 999);
            $order = Order::create([
                'order_code' => 'FST-'.Str::upper(Str::random(8)),
                'user_id' => $request->user()->id,
                'ticket_tier_id' => $tiers->first()->id,
                'quantity' => $data['quantity'],
                'day_pass' => $dayPass,
                'unique_code' => $code,
                'total_amount' => ($tiers->sum('price') * $data['quantity']) + $code,
                'status' => 'pending',
                'expires_at' => now()->addDay(),
            ]);
            foreach ($tiers as $tier) {
                $tier->increment('sold_count', $data['quantity']);
            }

            return $order;
        });

        return redirect()->route('dashboard')->with('success', "Pesanan {$order->order_code} berhasil dibuat. Bayar sesuai nominal yang tertera melalui GoPay, lalu unggah bukti pembayaran.");
    }

    public function uploadProof(StorePaymentProofRequest $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless(in_array($order->status, ['pending', 'rejected'], true), 422);
        abort_if(in_array($order->status, ['pending', 'rejected'], true) && $order->expires_at?->isPast(), 422, 'Batas waktu pembayaran telah berakhir. Silakan buat pesanan baru.');
        if ($order->payment_proof_path) {
            Storage::disk('local')->delete($order->payment_proof_path);
        }
        $path = $request->file('payment_proof')->store('payment_proofs', 'local');
        $order->update(['payment_proof_path' => $path, 'status' => 'waiting_verification', 'admin_note' => null]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi.');
    }

    public function download(Order $order)
    {
        abort_unless($order->user_id === auth()->id() && $order->status === 'verified', 404);
        $order->load(['user', 'ticketTier.festivalDay', 'tickets.ticketTier.festivalDay']);
        $qrCodes = $order->tickets->map(fn (Ticket $ticket): string => 'data:image/svg+xml;base64,'.base64_encode(QrCode::format('svg')->size(150)->generate($ticket->qr_payload)));
        $ticketDates = $order->tickets->pluck('ticketTier.festivalDay.event_date')->unique(fn ($date): string => $date->toDateString())->sort()->values();
        $ticketDateRange = $ticketDates->count() > 1
            ? $ticketDates->first()->locale('id')->translatedFormat('j').'–'.$ticketDates->last()->locale('id')->translatedFormat('j F Y')
            : $ticketDates->first()?->locale('id')->translatedFormat('j F Y');

        return Pdf::loadView('tickets.pdf', compact('order', 'qrCodes', 'ticketDateRange'))->download($order->order_code.'.pdf');
    }

    public function validateTicket(Ticket $ticket): View
    {
        abort_unless(request()->hasValidSignature(), 403);

        return view('tickets.validate', compact('ticket'));
    }
}
