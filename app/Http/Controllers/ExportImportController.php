<?php

namespace App\Http\Controllers;

use App\Jobs\ExportOrdersJob;
use App\Jobs\ImportOrdersJob;

class ExportImportController extends Controller
{
    /**
     * Export order data to excel.
     */
    public function export()
    {
        ExportOrdersJob::dispatch();

        return $this->trueResponse('Export order data is processing');
    }
    
    /**
     * Import order data to excel.
     */
    public function import()
    {
        ImportOrdersJob::dispatch();
        
        return $this->trueResponse('Import order data is processing');
    }
}
