<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ReportSalesTransaction implements FromView
{
    private $reports;

   public function __construct(array $sales)
   {
       $this->reports = $sales;
   }

   public function view(): View
   {
       return view('generate.excel.report-sales-transaction', $this->reports);
   }
}

