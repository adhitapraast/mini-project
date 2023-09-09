<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load(storage_path("export_orders.xlsx"));

        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $row = 2;
        $coordinates = [];
        $ordersData = [];

        for ($i = $row; $i < $highestRow && $i <= $highestRow; $i++) {
            $coordinates[] = [
                'C'.$i,
                'D'.$i,
                'E'.$i,
                'F'.$i,
                'G'.$i,
                'H'.$i,
                'I'.$i,
            ];
        }
        
        foreach($coordinates as $coordinate) {
            $usersId = $activeSheet->getCell($coordinate[0])->getValue();
            $totalAmount = $activeSheet->getCell($coordinate[1])->getValue();
            $status = (string) $activeSheet->getCell($coordinate[2])->getValue();
            $address = (string) $activeSheet->getCell($coordinate[3])->getValue();
            $createdAt = (string) $activeSheet->getCell($coordinate[4])->getValue();

            $ordersData[] = [
                'users_id' => $usersId,
                'total_amount' => $totalAmount,
                'status' => $status,
                'address' => $address,
                'created_at' => $createdAt,
                'updated_at' => now(),
            ];
        }

        Order::insert($ordersData);
    }
}
