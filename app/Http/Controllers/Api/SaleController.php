<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Sale\SaleHelper;
use Illuminate\Support\Facades\DB;
use App\Exports\LaporanPenjualanCSV;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportSalesTransaction;
use App\Http\Requests\Sale\SaleRequest;
use App\Http\Resources\Sale\SaleResource;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleDetailCollection;
use App\Http\Resources\Sale\SaleTransactionCollection;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;

class SaleController extends Controller
{
    private $sales;
    public function __construct()
    {
        $this->sales = new SaleHelper();
    }

    public function saleTransaction(Request $request)
    {
        $filter = [
            'start_date' => isset($request->start_date) ? $request->start_date : '',
            'end_date' => isset($request->end_date) ? $request->end_date : '',
            'm_customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
            'm_product_id' => isset($request->product_id) ? explode(',', $request->product_id) : [],
        ];
        // $startDate = $request->start_date ?? null;
        // $endDate = $request->end_date ?? null;
        // $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        // $productId = isset($request->product_id) ? explode(',', $request->product_id) : [];
        // $isExportExcel = $request->is_export_excel ?? null;
        // $isExportPdf = $request->is_export_pdf ?? null;

        $sales = $this->sales->getSaleTransaction($filter, $request->per_page ?? 25, $request->sort ?? '');

        // if ($isExportExcel) {
        //     return Excel::download(new ReportSalesTransaction($sales), 'report-sale-transaction.xls');
        // }
        // if ($isExportPdf) {
        //     return Pdf::download(new ReportSalesTransaction($sales), 'report-sale-transaction.pdf');
        // }

        return response()->success(new SaleTransactionCollection($sales['data']));
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

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function laporanCSV(Request $request)
    {
        $filter = [
            'start_date' => isset($request->start_date) ? $request->start_date : '',
            'end_date' => isset($request->end_date) ? $request->end_date : '',
            'm_customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
            'm_product_id' => isset($request->product_id) ? explode(',', $request->product_id) : [],
        ];

        $sales = $this->sales->getSaleTransactionForExport($filter);

        return Excel::download(new LaporanPenjualanCSV($sales['data']), 'Laporan Sales CSV - ' . Carbon::now() . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function laporanPDF(Request $request)
    {

        // Mengubah Format Waktu
        setlocale(LC_ALL, 'id-ID', 'id_ID');

        // Mengambil waktu sekarang dan merubah formatnya
        $waktu = strftime('%A, %d %B %Y');

        $filter = [
            'start_date' => isset($request->start_date) ? $request->start_date : '',
            'end_date' => isset($request->end_date) ? $request->end_date : '',
            'm_customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
            'm_product_id' => isset($request->product_id) ? explode(',', $request->product_id) : [],
        ];

        // Mengambil Data Joinan Sales
        // $sales = DB::table('t_sales_detail')
        //     ->join('t_sales', 't_sales_detail.t_sales_id', 't_sales.id')
        //     ->join('m_customer', 't_sales.m_customer_id', 'm_customer.id')
        //     ->join('m_product', 't_sales_detail.m_product_id', 'm_product.id')
        //     ->select(
        //                 '*',
        //                 'm_customer.name as nama_customer',
        //                 'm_product.name as nama_menu',
        //                 'm_product.price as harga_menu',
        //                 't_sales_detail.price as harga_total'
        //             )
        //     ->get();

        $sales = $this->sales->getSaleTransactionForExport($filter);
        // dd($this->sales);
        // Menampilkan PDF beserta data yang sudah dijoin dan mengubah orientasi PDF
        $pdf = Pdf::loadView('generate.pdf.report-sales', ['sales' => $sales['data'], 'waktu' => $waktu])->setPaper('a4', 'landscape');

        // Melakukan Unduh File
        return $pdf->download('Laporan Sales PDF - ' . Carbon::now() . '.pdf');

        // return response()->success($sales['data'], '', [
        //     'dates'          => $sales['dates'] ?? [],
        //     'total_per_date' => $sales['total_per_date'] ?? [],
        //     'grand_total'    => $sales['grand_total'] ?? 0
        // ]);

    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function testingSales(Request $request)
    {
        $filter = [
            'start_date' => isset($request->start_date) ? $request->start_date : '',
            'end_date' => isset($request->end_date) ? $request->end_date : '',
            'm_customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
            'm_product_id' => isset($request->product_id) ? explode(',', $request->product_id) : [],
        ];


        $sales = $this->sales->getSaleTransaction($filter, $request->per_page ?? 25, $request->sort ?? '');
        // dd($sales['data']);
        return view('test', ['sales' => $sales['data']]);
    }

}
