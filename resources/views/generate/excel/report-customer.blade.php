<table>
    <thead>
        <tr>
            <th id="customer">Customer</th>
            @foreach($dates as $date)
                <th>
                    {{date('d', strtotime($date))}}
                </th>
            @endforeach
            <th id="total">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $saleCustomer)
         <tr>
            <td>
                {{$saleCustomer['customer_name']}}
            </td>
            @foreach($saleCustomer['transactions'] as $transaction)
            <td >
                Rp {{number_format($transaction['total_buy'])}}
            </td>
            @endforeach
            <td class="nominal">
                Rp {{number_format($saleCustomer['customer_total'])}}
            </td>
         </tr>
        @endforeach
        <tr>
            <td >Grand Total</td>
            @foreach($total_per_date as $total)
            <td class="nominal">
                Rp {{number_format($total)}}
            </td>
            @endforeach
            <td class="nominal">
                Rp {{number_format($grand_total)}}
            </td>
        </tr>
    </tbody>
</table>
