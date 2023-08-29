<?php

namespace App\Http\Resources\Sale;

use App\Http\Resources\Sale\SaleDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'customer_name' => $this->customer->name ?? null,
            'voucher_id' => $this->voucher->id ?? null,
            'voucher_name' => $this->voucher->promo->name ?? null,
            'discount_id' => $this->discount->id ?? null,
            'discount_name' => $this->discount->promo->name ?? null,
            'discount_percentage' => $this->discount->promo->nominal_percentage ?? 0,
            'voucher_nominal' => $this->voucher_nominal,
            'date' => $this->date,
            'details' => SaleDetailResource::collection($this->details)
        ];
    }

}
