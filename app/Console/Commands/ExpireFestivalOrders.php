<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireFestivalOrders extends Command
{
    protected $signature = 'festival:expire-orders';

    protected $description = 'Cancel unpaid festival orders after the 24-hour payment window';

    public function handle(): int
    {
        Order::query()->whereIn('status', ['pending', 'rejected'])->where('expires_at', '<=', now())->orderBy('id')->chunkById(100, function ($orders): void {
            foreach ($orders as $order) {
                DB::transaction(function () use ($order): void {
                    $lockedOrder = Order::query()->lockForUpdate()->find($order->id);
                    if (! $lockedOrder || ! in_array($lockedOrder->status, ['pending', 'rejected'], true) || $lockedOrder->expires_at?->isFuture()) {
                        return;
                    }
                    $tiers = $lockedOrder->day_pass
                        ? $lockedOrder->ticketTier->newQuery()->where('zone_name', $lockedOrder->ticketTier->zone_name)->orderBy('festival_day_id')->lockForUpdate()->get()
                        : collect([$lockedOrder->ticketTier]);
                    foreach ($tiers as $tier) {
                        $tier->decrement('sold_count', $lockedOrder->quantity);
                    }
                    $lockedOrder->update(['status' => 'cancelled']);
                });
            }
        });

        return self::SUCCESS;
    }
}
