<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class SaleRequest extends FormRequest
{
    public $validator;
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules():array
    {
        return [
            'customer_id' => 'required|string',
            'voucher_nominal' => 'required|numeric',
            'date' => 'required',
            'details.*.product_detail_id' => 'nullable|string',
            'details.*.product_id' => 'required|string',
            'details.*.total_item' => 'required|numeric',
            'details.*.price' => 'nullable|numeric',
            'details.*.discount_nominal' => 'nullable|numeric'
        ];
    }

    private function updateRules():array
    {
        return [
            'id' => 'required|string',
            'customer_id' => 'required|string',
            'voucher_id' => 'required|string',
            'discount_id' => 'required|string',
            'voucher_nominal' => 'required|numeric',
            'date' => 'required|date'
        ];
    }

    public function attributes()
    {
        return [
            'voucher_nominal' => 'Nominal Voucher'
        ];
    }
}
