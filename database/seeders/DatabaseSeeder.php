<?php

namespace Database\Seeders;

use App\Models\FestivalDay;
use App\Models\TicketTier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@festival.com'], ['name' => 'Admin AFTERGLOW', 'phone' => '+62 812 0000 0000', 'password' => Hash::make('FestivalAdmin!2026'), 'role' => 'admin']);
        User::firstOrCreate(['email' => 'test@example.com'], ['name' => 'Pengunjung AFTERGLOW', 'phone' => '+62 812 1111 1111', 'password' => Hash::make('password'), 'role' => 'user']);
        $days = [
            [1, 'Day One - From Sunset Confessions to Midnight Anthems', '2026-12-29', 'Day 01 AFTERGLOW di Kebun Raya Bogor: dari pengakuan saat senja hingga anthem tengah malam.'],
            [2, 'Day Two - Malam Paling Jujur di Penghujung Tahun', '2026-12-30', 'Day 02 AFTERGLOW, malam paling jujur untuk menyambut akhir tahun bersama.'],
            [3, 'Day Three - SING UNTIL MIDNIGHT, DANCE INTO 2027', '2026-12-31', 'Day 03 AFTERGLOW menyambut tahun baru dengan musik hingga melewati tengah malam.'],
        ];
        $zones = [
            'Regular' => [120000, 1200, ['Akses area festival', 'Akses area kuliner']],
            'Premium' => [200000, 450, ['Jalur masuk prioritas', 'Area menonton Premium', 'Bar khusus']],
            'Platinum' => [500000, 120, ['Area menonton depan panggung', 'Akses lounge', 'Jalur masuk cepat', 'Minuman sambutan']],
        ];
        foreach ($days as [$number, $title, $date, $description]) {
            $day = FestivalDay::firstOrCreate(['day_number' => $number], ['title' => $title, 'event_date' => $date, 'description' => $description]);
            foreach ($zones as $zone => [$dayOnePrice, $quota, $benefits]) {
                $price = match ($number) {
                    1 => $dayOnePrice,
                    2 => ['Regular' => 150000, 'Premium' => 230000, 'Platinum' => 700000][$zone],
                    3 => ['Regular' => 250000, 'Premium' => 320000, 'Platinum' => 1500000][$zone],
                };
                TicketTier::firstOrCreate(['festival_day_id' => $day->id, 'zone_name' => $zone], ['price' => $price, 'quota' => $quota, 'benefits' => $benefits]);
            }
        }
    }
}
