<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketTier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()->with(['user', 'ticketTier.festivalDay'])->latest()->get();
        $tiers = TicketTier::query()->with('festivalDay')->orderBy('festival_day_id')->get();
        $revenue = Order::query()->where('status', 'verified')->sum('total_amount');
        $pending = Order::query()->where('status', 'waiting_verification')->count();
        $sold = Ticket::query()->whereHas('order', fn ($query) => $query->where('status', 'verified'))->count();
        $revenueByDay = Ticket::query()->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_tiers', 'tickets.ticket_tier_id', '=', 'ticket_tiers.id')
            ->join('festival_days', 'ticket_tiers.festival_day_id', '=', 'festival_days.id')
            ->where('orders.status', 'verified')->groupBy('festival_days.day_number')->orderBy('festival_days.day_number')
            ->selectRaw('festival_days.day_number, SUM(orders.total_amount / orders.quantity / CASE WHEN orders.day_pass = 1 THEN 3 ELSE 1 END) as revenue')->get();
        $ticketDistribution = Ticket::query()->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_tiers', 'tickets.ticket_tier_id', '=', 'ticket_tiers.id')
            ->join('festival_days', 'ticket_tiers.festival_day_id', '=', 'festival_days.id')
            ->where('orders.status', 'verified')->groupBy('festival_days.day_number', 'ticket_tiers.zone_name')
            ->selectRaw('festival_days.day_number, ticket_tiers.zone_name, COUNT(tickets.id) as quantity')->get();
        $revenueChartData = $revenueByDay->map(fn ($row): array => [
            'day' => 'DAY '.str_pad($row->day_number, 2, '0', STR_PAD_LEFT),
            'revenue' => (int) $row->revenue,
        ])->values();
        $ticketChartData = $ticketDistribution->map(fn ($row): array => [
            'day' => (int) $row->day_number,
            'zone' => $row->zone_name,
            'quantity' => (int) $row->quantity,
        ])->values();

        return view('admin.dashboard', compact('orders', 'tiers', 'revenue', 'pending', 'sold', 'revenueByDay', 'ticketDistribution', 'revenueChartData', 'ticketChartData'));
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new OrdersExport, 'festival-sales.xlsx');
    }

    public function updateTier(Request $request, TicketTier $ticketTier): RedirectResponse
    {
        $data = $request->validate([
            'price' => ['required', 'integer', 'min:1'],
            'quota' => ['required', 'integer', 'min:'.$ticketTier->sold_count],
        ]);
        DB::transaction(function () use ($data, $ticketTier): void {
            $lockedTier = TicketTier::query()->lockForUpdate()->findOrFail($ticketTier->id);
            abort_if($data['quota'] < $lockedTier->sold_count, 422, 'Kuota tidak dapat lebih rendah dari jumlah tiket yang sudah dipesan.');
            $lockedTier->update($data);
        });

        return back()->with('success', 'Harga dan kuota tiket berhasil diperbarui.');
    }
}
