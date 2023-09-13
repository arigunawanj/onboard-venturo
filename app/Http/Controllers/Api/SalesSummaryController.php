<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Report\TotalSalesHelper;

class SalesSummaryController extends Controller
{
    private $sales;

    public function __construct()
    {
        $this->sales    = new TotalSalesHelper();
    }

    public function getDiagramPerYear()
    {
        $sales = $this->sales->getTotalPerYear();

        return response()->success($sales['data']);
    }

    public function getTotalSummary()
    {
        $sales = $this->sales->getTotalInPeriode();


        return response()->success($sales['data']);
    }

    public function getTotalperMonth()
    {
        $sales = $this->sales->ambilTotalPerBulan();


        return response()->success($sales['data']);
    }
}
