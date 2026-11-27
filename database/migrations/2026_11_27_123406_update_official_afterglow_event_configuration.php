<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update public festival configuration without changing order snapshots or inventory.
     */
    public function up(): void
    {
        $days = [
            1 => [
                'title' => 'Day One - From Sunset Confessions to Midnight Anthems',
                'event_date' => '2026-12-29',
                'description' => 'Day 01 AFTERGLOW di Kebun Raya Bogor: dari pengakuan saat senja hingga anthem tengah malam.',
            ],
            2 => [
                'title' => 'Day Two - Malam Paling Jujur di Penghujung Tahun',
                'event_date' => '2026-12-30',
                'description' => 'Day 02 AFTERGLOW, malam paling jujur untuk menyambut akhir tahun bersama.',
            ],
            3 => [
                'title' => 'Day Three - SING UNTIL MIDNIGHT, DANCE INTO 2027',
                'event_date' => '2026-12-31',
                'description' => 'Day 03 AFTERGLOW menyambut tahun baru dengan musik hingga melewati tengah malam.',
            ],
        ];

        $prices = [
            1 => ['Regular' => 120000, 'Premium' => 200000, 'Platinum' => 500000],
            2 => ['Regular' => 150000, 'Premium' => 230000, 'Platinum' => 700000],
            3 => ['Regular' => 250000, 'Premium' => 320000, 'Platinum' => 1500000],
        ];

        $benefits = [
            'Regular' => ['Akses area festival', 'Akses area kuliner'],
            'Premium' => ['Jalur masuk prioritas', 'Area menonton Premium', 'Bar khusus'],
            'Platinum' => ['Area menonton depan panggung', 'Akses lounge', 'Jalur masuk cepat', 'Minuman sambutan'],
        ];

        foreach ($days as $dayNumber => $attributes) {
            DB::table('festival_days')->where('day_number', $dayNumber)->update($attributes);

            $festivalDayId = DB::table('festival_days')->where('day_number', $dayNumber)->value('id');

            if ($festivalDayId === null) {
                continue;
            }

            foreach ($prices[$dayNumber] as $zoneName => $price) {
                DB::table('ticket_tiers')
                    ->where('festival_day_id', $festivalDayId)
                    ->where('zone_name', $zoneName)
                    ->update([
                        'price' => $price,
                        'benefits' => json_encode($benefits[$zoneName], JSON_UNESCAPED_UNICODE),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Keep event pricing and dates intact during rollback to avoid overwriting managed configuration.
    }
};
