<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromQuery, WithColumnFormatting, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    public function query(): Builder
    {
        return Order::query()->with(['user', 'ticketTier.festivalDay'])->orderBy('id');
    }

    public function headings(): array
    {
        return ['Order ID', 'Buyer Name', 'Email', 'Festival Day', 'Event Date', 'Zone', 'Quantity', 'Total Amount', 'Payment Status', 'Verification Date'];
    }

    public function map(mixed $row): array
    {
        /** @var Order $order */
        $order = $row;
        $festivalDay = $order->ticketTier->festivalDay;
        $eventDate = $order->day_pass
            ? $festivalDay->event_date->locale('id')->translatedFormat('j F Y').' – '.$festivalDay->event_date->copy()->addDays(2)->locale('id')->translatedFormat('j F Y')
            : Date::dateTimeToExcel($festivalDay->event_date->copy()->startOfDay());

        return [
            $order->order_code,
            $order->user->name,
            $order->user->email,
            $order->day_pass ? 'Day 01–03' : 'Day '.str_pad($festivalDay->day_number, 2, '0', STR_PAD_LEFT),
            $eventDate,
            $order->ticketTier->zone_name,
            $order->quantity,
            (int) $order->total_amount,
            $order->status,
            $order->verified_at ? Date::dateTimeToExcel($order->verified_at) : null,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => 'dd mmmm yyyy',
            'H' => '[$Rp-421] #,##0',
            'J' => 'dd mmmm yyyy hh:mm',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 26,
            'C' => 32,
            'D' => 20,
            'E' => 30,
            'F' => 16,
            'G' => 14,
            'H' => 20,
            'I' => 22,
            'J' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:J1');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '20211E']],
            ],
        ];
    }
}
