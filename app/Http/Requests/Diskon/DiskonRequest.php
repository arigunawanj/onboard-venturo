<?php

namespace App\Http\Requests\Diskon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class DiskonRequest extends FormRequest
{

    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() :array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules():array
    {
        return [
            'customer_id' => 'required',
            'promo_id' => 'required',
            'is_status' => 'required',
        ];
    }

    private function updateRules():array
    {
        return [
            'id' => 'required',
            'customer_id' => 'required',
            'promo_id' => 'required',
            'is_status' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => 'Customer',
            'promo_id' => 'Voucher',
            'status' => 'Status'
        ];
    }
}
