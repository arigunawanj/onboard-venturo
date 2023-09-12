
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <td rowspan="2" class="text-center">No</td>
                    <td rowspan="2" class="text-center">No Struk</td>
                    <td rowspan="2" class="text-center">Customer</td>
                    <td rowspan="2" class="text-center">Tanggal</td>
                    <td colspan="2" class="text-center">Promo</td>
                    <td rowspan="2" class="text-center">Menu</td>
                    <td rowspan="2" class="text-center">Jumlah</td>
                    <td rowspan="2" class="text-center">Harga</td>
                    <td rowspan="2" class="text-center">Total</td>
                    <td rowspan="2" class="text-center">Total Harga</td>
                </tr>
                <tr>
                    <td class="text-center">Diskon</td>
                    <td class="text-center">Voucher</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales->unique('t_sales_id') as $item)
                    <tr>
                        <td class="vertical-middle">{{ $loop->iteration }}</td>
                        <td class="vertical-middle">{{ $item->sales->invoice }}</td>
                        <td class="vertical-middle">{{$item->sales->customer->name }}</td>
                        <td class="vertical-middle">{{$item->sales->date}}</td>
                        <td class="vertical-middle">
                            {{ $item->sales->discount->nominal_percentage ?? '-' }}
                        </td>
                        <td class="vertical-middle">{{ $item->sales->discount->nominal_rupiah ?? '-'}}</td>
                        <td class="vertical-middle">{{ $item->product->name}}</td>
                        <td class="vertical-middle">{{  $item->total_item }}</td>
                        <td class="vertical-middle">{{ $item->price }}</td>
                        <td class="vertical-middle">{{ $item->price * $item->total_item }}</td>
                        <td class="vertical-middle">{{ $item->price * $item->total_item }}</td>
                    </tr>

                @endforeach
            </tbody>
        </table>

    </div>
</body>
</html>

