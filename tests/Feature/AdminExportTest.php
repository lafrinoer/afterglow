<?php

namespace Tests\Feature;

use App\Exports\OrdersExport;
use App\Models\Order;
use App\Models\TicketTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class AdminExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_export_downloads_xlsx_with_database_rows_and_expected_columns(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $admin = User::where('email', 'admin@festival.com')->firstOrFail();
        $tier = TicketTier::whereHas('festivalDay', fn ($query) => $query->where('day_number', 1))
            ->where('zone_name', 'Regular')->firstOrFail();
        Order::create([
            'order_code' => 'FST-EXAMPLE01',
            'user_id' => $user->id,
            'ticket_tier_id' => $tier->id,
            'quantity' => 2,
            'unique_code' => 123,
            'total_amount' => 240123,
            'status' => 'verified',
            'verified_at' => '2026-12-29 16:30:00',
        ]);

        $this->actingAs($admin)->get(route('admin.export'))
            ->assertOk()
            ->assertDownload('festival-sales.xlsx')
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $workbook = $this->loadExportWorkbook();
        $sheet = $workbook->getActiveSheet();
        $rows = $sheet->rangeToArray('A1:J2', null, true, false);

        $this->assertSame([
            'Order ID', 'Buyer Name', 'Email', 'Festival Day', 'Event Date', 'Zone', 'Quantity', 'Total Amount', 'Payment Status', 'Verification Date',
        ], $rows[0]);
        $this->assertSame('FST-EXAMPLE01', $rows[1][0]);
        $this->assertSame('Pengunjung AFTERGLOW', $rows[1][1]);
        $this->assertSame('Day 01', $rows[1][3]);
        $this->assertSame('Regular', $rows[1][5]);
        $this->assertSame(2, $rows[1][6]);
        $this->assertSame(240123, $rows[1][7]);
        $this->assertSame('[$Rp-421] #,##0', $sheet->getStyle('H2')->getNumberFormat()->getFormatCode());
        $this->assertSame('2026-12-29', Date::excelToDateTimeObject((float) $rows[1][4])->format('Y-m-d'));
        $this->assertSame('2026-12-29 16:30', Date::excelToDateTimeObject((float) $rows[1][9])->format('Y-m-d H:i'));
    }

    public function test_admin_export_returns_a_valid_heading_only_workbook_when_there_are_no_orders(): void
    {
        $admin = User::where('email', 'admin@festival.com')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.export'))
            ->assertOk()
            ->assertDownload('festival-sales.xlsx');

        $sheet = $this->loadExportWorkbook()->getActiveSheet();
        $this->assertSame(1, $sheet->getHighestDataRow());
        $this->assertSame('Order ID', $sheet->getCell('A1')->getValue());
        $this->assertSame('Verification Date', $sheet->getCell('J1')->getValue());
    }

    public function test_regular_users_cannot_download_transaction_exports(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->actingAs($user)->get(route('admin.export'))->assertForbidden();
    }

    private function loadExportWorkbook(): Spreadsheet
    {
        $path = tempnam(sys_get_temp_dir(), 'afterglow-export-');
        file_put_contents($path, app('excel')->raw(new OrdersExport, Excel::XLSX));

        try {
            return IOFactory::load($path);
        } finally {
            @unlink($path);
        }
    }
}
