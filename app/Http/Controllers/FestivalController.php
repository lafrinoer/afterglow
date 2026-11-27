<?php

namespace App\Http\Controllers;

use App\Models\FestivalDay;
use Illuminate\Contracts\View\View;

class FestivalController extends Controller
{
    public function index(): View
    {
        $days = FestivalDay::query()->with('ticketTiers')->orderBy('day_number')->get();
        $lineup = config('festival.lineup');
        $groundsPrices = $days->mapWithKeys(fn (FestivalDay $day): array => [
            $day->day_number => $day->ticketTiers->mapWithKeys(fn ($tier): array => [$tier->zone_name => $tier->price])->all(),
        ])->all();
        $zoneBenefits = $days->first()?->ticketTiers->keyBy('zone_name') ?? collect();

        return view('welcome', compact('days', 'lineup', 'groundsPrices', 'zoneBenefits'));
    }
}
