<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    table.sales {
        border: 1px solid #1C6EA4;
        background-color: #EEEEEE;
        width: 100%;
        text-align: center;
        border-collapse: collapse;
    }

    table.sales td,
    table.sales th {
        border: 1px solid #AAAAAA;
        padding: 3px 2px;
    }

    table.sales tbody td {
        font-size: 13px;
    }

    table.sales tr:nth-child(even) {
        background: #D0E4F5;
    }

    table.sales thead {
        background: #1C6EA4;
        background: -moz-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
        background: -webkit-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
        background: linear-gradient(to bottom, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
        border-bottom: 2px solid #444444;
    }

    table.sales thead th {
        font-size: 15px;
        font-weight: bold;
        color: #FFFFFF;
        text-align: center;
        border-left: 2px solid #D0E4F5;
    }

    table.sales thead th:first-child {
        border-left: none;
    }

    table.sales tfoot {
        font-size: 14px;
        font-weight: bold;
        color: #FFFFFF;
        background: #D0E4F5;
        background: -moz-linear-gradient(top, #dcebf7 0%, #d4e6f6 66%, #D0E4F5 100%);
        background: -webkit-linear-gradient(top, #dcebf7 0%, #d4e6f6 66%, #D0E4F5 100%);
        background: linear-gradient(to bottom, #dcebf7 0%, #d4e6f6 66%, #D0E4F5 100%);
        border-top: 2px solid #444444;
    }

    table.sales tfoot td {
        font-size: 14px;
    }

    table.sales tfoot .links {
        text-align: right;
    }

    table.sales tfoot .links a {
        display: inline-block;
        background: #1C6EA4;
        color: #FFFFFF;
        padding: 2px 8px;
        border-radius: 5px;
    }

    .text-center {
        text-align: center;
        margin-top: -15px
    }
</style>

<body>
    <header>
        <h1 class="text-center">Laporan Penjualan</h1>
        <p class="text-center" id="tanggal">
            Dicetak : <span class="text-warna">{{ $waktu }}</span>
        </p>
    </header>
    <table class="sales">
        <thead>
            <tr>
                <th>No Struk</th>
                <th>Customer</th>
                <th>Tanggal Pembelian</th>
                <th>Diskon</th>
                <th>Voucher</th>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $item)
                <tr>
                    <td>{{ $item->sales->invoice }}</td>
                    <td>{{ $item->sales->customer->name }}</td>
                    @php
                        $tanggal = date_create($item->date);
                        $tgl = \Carbon\Carbon::parse($tanggal)->formatLocalized('%d %B %Y');
                    @endphp
                    <td>{{ $tgl }}</td>
                    <td>{{ $item->sales->discount->nominal_percentage ?? "-"  }}</td>
                    <td>{{ $item->sales->voucher->nominal_rupiah ?? "-"  }}</td>
                    <td>{{ $item->product->name ?? "-"}}</td>
                    <td>{{ number_format($item->total_item, 0, '.', '.') }}</td>
                    <td>{{ number_format($item->price, 0, '.', '.') }}</td>
                    <td>{{ number_format($item->total_item * $item->price, 0, '.', '.') }}</td>
                    <td>{{ number_format($item->total_item * $item->price, 0, '.', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
