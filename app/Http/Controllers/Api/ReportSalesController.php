<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Exports\ReportSalesCategory;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\Report\SalesPromoHelper;
use App\Http\Resources\Report\SalesPromoCollection;

class ReportSalesController extends Controller
{
    private $salesCategory;
    private $salesPromo;
    public function __construct()
    {
        $this->salesPromo = new SalesPromoHelper();
    }

    public function viewSalesPromo(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $promoId = isset($request->promo_id) ? explode(',', $request->promo_id) : [];

        $sales = $this->salesPromo->get($startDate, $endDate, $customerId, $promoId);

        // $coba = json_decode($sales['data']);
        // dd($sales);

        return response()->success(new SalesPromoCollection($sales['data']));
    }

    public function viewSalesCategories(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $categoryId    = $request->category_id ?? null;
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCategory->get($startDate, $endDate, $categoryId);

        if ($isExportExcel) {
            // dd($sales);
            return Excel::download(new ReportSalesCategory($sales), 'report-sales-category.xls');
        }

        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }
}
