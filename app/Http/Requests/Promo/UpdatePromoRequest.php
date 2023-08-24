<?php

namespace App\Http\Requests\Promo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UpdatePromoRequest extends FormRequest
{
    use ConvertsBase64ToFiles;
    public $validator = null;

    public function attributes()
    {
        return [
            'name' => 'Nama Promo',
            'nominal_percentage' => 'Nominal Persentase',
            'nominal_rupiah' => 'Nominal Rupiah',
            'term_conditions' => 'Syarat dan Ketentuan',
            'expired_in_day' => 'Kadaluarsa Promo',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required',
            'name' => 'required',
            'expired_in_day' => 'nullable',
            'nominal_percentage' => 'numeric|required_if:status,diskon|nullable',
            'nominal_rupiah' => 'numeric|required_if:status,voucher|nullable',
            'term_conditions' => 'required',
            'photo' => 'nullable',
        ];
    }

    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-user.jpg',
        ];
    }
}
