<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\Sale\SaleHelper;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportSalesTransaction;
use App\Http\Requests\Sale\SaleRequest;
use App\Http\Resources\Sale\SaleResource;
use App\Http\Resources\Sale\SaleCollection;
use App\Models\Sales;
use App\Models\SalesModel;

class SaleController extends Controller
{
    private $sales;
    public function __construct()
    {
        $this->sales = new SaleHelper();
    }

    public function index(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $promoId = isset($request->promo_id) ? explode(',', $request->promo_id) : [];
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->sales->getAll($startDate, $endDate, $customerId, $promoId);

        if ($isExportExcel) {
            return Excel::download(new ReportSalesTransaction($sales), 'report-transaction.xls');
        }

        return response()->success(new SaleCollection($sales['data']));
    }

    public function store(SaleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['customer_id', 'voucher_id', 'discount_id', 'voucher_nominal', 'date', 'details']);
        $payload = $this->renamePayload($payload);
        $sales = $this->sales->create($payload);

        if (!$sales['status']) {
            return response()->failed($sales['error']);
        }

        return response()->success(new SaleResource($sales['data']), 'sales berhasil ditambahkan');
    }

    public function show($id)
    {
        $sales = $this->sales->getById($id);

        if (!($sales['status'])) {
            return response()->failed(['Data sales tidak ditemukan'], 404);
        }

        return response()->success(new SaleResource($sales['data']));
    }

    public function update(SaleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'customer_id', 'sales_id', 'discount_id', 'sales_nominal', 'date']);
        $payload = $this->renamePayload($payload);
        $sales = $this->sales->update($payload);

        if (!$sales['status']) {
            return response()->failed($sales['error']);
        }

        return response()->success(new SaleResource($sales['data']), 'sales berhasil diubah');
    }

    public function destroy($id)
    {
        $sales = $this->sales->delete($id);

        if (!$sales) {
            return response()->failed(['Mohon maaf sales tidak ditemukan']);
        }

        return response()->success($sales, 'sales berhasil dihapus');
    }

    public function renamePayload($payload)
    {
        $payload['m_customer_id'] = $payload['customer_id'] ?? null;
        $payload['m_voucher_id'] = $payload['voucher_id'] ?? null;
        $payload['m_discount_id'] = $payload['discount_id'] ?? null;
        unset($payload['customer_id']);
        unset($payload['voucher_id']);
        unset($payload['discount_id']);
        return $payload;
    }
}
