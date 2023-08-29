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
            'product_id' => $this->product->id ?? null,
            'product_name' => $this->product->name ?? null,
            'product_detail_id' => $this->productDetail->id ?? null,
            'product_detail_description' => $this->productDetail->description ?? null,
            'total_item' => $this->total_item,
            'discount_nominal' => $this->discount_nominal,
            'price' => $this->price
        ];
    }

}
