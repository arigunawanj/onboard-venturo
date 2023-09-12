<?php

namespace App\Exports;

use App\Models\SalesModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanPenjualanCSV implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings
{
    private $data;

    public function __construct($salesData)
    {
        $this->data = $salesData;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No Struk',
            'Customer',
            'Tanggal Pembelian',
            'Diskon',
            'Voucher',
            'Menu',
            'Jumlah',
            'Harga',
            'Total',
            'Total Harga'
        ];
    }

    public function map($sales): array
    {
        return [
            $sales->sales->invoice,
            $sales->sales->customer->name,
            $sales->sales->date,
            $sales->sales->discount->nominal_percentage ?? "-",
            $sales->sales->voucher->nominal_rupiah ?? "-",
            $sales->product->name,
            $sales->total_item,
            $sales->price,
            $sales->total_item * $sales->price,
            $sales->total_item * $sales->price,
        ];
    }

}
