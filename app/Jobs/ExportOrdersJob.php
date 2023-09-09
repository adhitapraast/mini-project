<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportOrdersJob implements ShouldQueue
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
        $spreadsheet = new Spreadsheet();
        $Excel_writer = new Xlsx($spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet()->setShowGridlines(false)->setTitle('Export Orders');

        $activeSheet->setCellValue('A1', 'No.');
        $activeSheet->setCellValue('B1', 'ID');
        $activeSheet->setCellValue('C1', 'users_id');
        $activeSheet->setCellValue('D1', 'total_amount');
        $activeSheet->setCellValue('E1', 'status');
        $activeSheet->setCellValue('F1', 'address');
        $activeSheet->setCellValue('G1', 'created_at');
        $activeSheet->setCellValue('H1', 'order_items_id');
        $activeSheet->setCellValue('I1', 'quantity');
        $activeSheet->setCellValue('J1', 'price');
        $activeSheet->setCellValue('K1', 'products_id');
        $activeSheet->setCellValue('L1', 'products_description');
        $activeSheet->setCellValue('M1', 'products_name');

        $activeSheet->getStyle('A1:M3')->getFont()->setBold(true);

        foreach (range('A1', 'M1') as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        DB::table('orders')->select(DB::raw(
                'orders.id AS orders_id,' . 
                'orders.users_id,' . 
                'orders.total_amount,' . 
                'orders.status,' . 
                'orders.address,' . 
                'orders.created_at,' . 
                'order_items.id AS order_items_id,' . 
                'order_items.quantity AS quantity,' . 
                'order_items.price AS price,' . 
                'products.id AS products_id,' . 
                'products.description AS products_description,' .
                'products.name AS products_name' 
            ))
            ->join('order_items', function ($join) {
                $join->on('order_items.orders_id', '=', 'orders.id');
            })
            ->join('products', function ($join) {
                $join->on('order_items.products_id', '=', 'products.id');
            })
            ->limit(200)
            ->orderBy('orders_id')->chunkById(1000, function ($chunksData) use (&$activeSheet) {
                $i = 2;
                $no = 1;
                
                foreach ($chunksData as $data) {
                    $activeSheet->setCellValue("A{$i}", $no);
                    $activeSheet->setCellValue("B{$i}", $data->orders_id);
                    $activeSheet->setCellValue("C{$i}", $data->users_id);
                    $activeSheet->setCellValue("D{$i}", $data->total_amount);
                    $activeSheet->setCellValue("E{$i}", $data->status);
                    $activeSheet->setCellValue("F{$i}", $data->address);
                    $activeSheet->setCellValue("G{$i}", $data->created_at);
                    $activeSheet->setCellValue("H{$i}", $data->order_items_id);
                    $activeSheet->setCellValue("I{$i}", $data->quantity);
                    $activeSheet->setCellValue("J{$i}", $data->price);
                    $activeSheet->setCellValue("K{$i}", $data->products_id);
                    $activeSheet->setCellValue("L{$i}", $data->products_name);
                    $activeSheet->setCellValue("M{$i}", $data->products_description);
                    $i++;
                    $no++;
                }
            }, 'orders_id');

            $filename = "export_orders.xlsx";
            $Excel_writer->save(storage_path($filename));
    }
}
