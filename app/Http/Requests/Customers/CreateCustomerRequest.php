<?php

namespace App\Http\Requests\Customers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class CreateCustomerRequest extends FormRequest
{

    use ConvertsBase64ToFiles;

    public $validator;

    public function attributes()
    {
        return [
            'name' => 'Nama Pengguna',
            'email' => 'Email',
            'phone_number' => 'Nomor Telepon',
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
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phone_number' => 'numeric',
            'photo' => 'nullable|file|image',
        ];
    }

    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-user.jpg',
        ];
    }
}
