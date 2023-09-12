<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
             'id' => $this->id,
            'sales_id' => $this->t_sales_id,
            'invoice' => $this->sales->invoice,
            'date' => $this->sales->date,
            'discount' => $this->sales->discount->nominal_percentage ?? null,
            'voucher' => $this->sales->voucher->nominal_rupiah ?? null,
            'product_id' => $this->product->id ?? null,
            'product_name' => $this->product->name ?? null,
            'total_item' => $this->total_item,
            // 'discount_nominal' => $this->discount_nominal,
            'price' => $this->price,
            'customer_name' => $this->sales->customer->name ?? null,
        ];
    }

}
