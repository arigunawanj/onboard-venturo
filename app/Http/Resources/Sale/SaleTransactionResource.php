<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleTransactionResource extends JsonResource
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
            'customer_id' => $this->customer->id ?? null,
            'invoice' => $this->invoice ?? null,
            'customer_name' => $this->customer->name ?? null,
            'voucher_id' => $this->voucher->id ?? null,
            'voucher_name' => $this->voucher->promo->name ?? null,
            'discount_id' => $this->discount->id ?? null,
            'discount_name' => $this->discount->name ?? null,
            'discount_percentage' => $this->discount->nominal_percentage ?? null,
            'voucher_nominal' => $this->voucher_nominal ?? null,
            'date' => $this->date,
            'details' => SaleDetailResource::collection($this->details)
        ];
    }
}
