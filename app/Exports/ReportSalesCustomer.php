<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ReportSalesCustomer implements FromView
{
    private $reports;

    public function __construct(array $sales)
    {
        $this->reports = $sales;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() :View
    {
        return view('generate.excel.report-customer', $this->reports);
    }
}
