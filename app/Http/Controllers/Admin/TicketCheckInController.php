<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketCheckInController extends Controller
{
    public function index(): View
    {
        return view('admin.checkin');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['ticket_code' => ['required', 'uuid']]);
        $result = DB::transaction(function () use ($data): array {
            $ticket = Ticket::query()->with('ticketTier.festivalDay')->lockForUpdate()->where('ticket_code', $data['ticket_code'])->first();
            if (! $ticket) {
                return ['error' => 'Kode tiket tidak ditemukan.'];
            }
            if ($ticket->ticketTier->festivalDay->event_date->toDateString() !== today()->toDateString()) {
                return ['error' => 'Tiket ini berlaku pada '.$ticket->ticketTier->festivalDay->event_date->locale('id')->translatedFormat('j F Y').', bukan hari ini.'];
            }
            if ($ticket->is_checked_in) {
                return ['error' => 'Tiket ini sudah digunakan untuk check-in.'];
            }
            $ticket->update(['is_checked_in' => true, 'checked_in_at' => now()]);

            return ['success' => 'Tiket valid. Check-in berhasil untuk zona '.$ticket->ticketTier->zone_name.'.'];
        });
        if (isset($result['error'])) {
            return back()->withErrors(['ticket_code' => $result['error']]);
        }

        return back()->with('success', $result['success']);
    }
}
