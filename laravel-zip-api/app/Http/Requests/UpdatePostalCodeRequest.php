<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostalCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|size:4|unique:postal_codes,code,' . $this->postal_code->id,
            'city_id' => 'required|exists:cities,id'
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Az irányítószám megadása kötelező',
            'code.size' => 'Az irányítószám pontosan 4 karakter hosszú kell legyen',
            'code.unique' => 'Ez az irányítószám már létezik',
            'city_id.required' => 'A település megadása kötelező',
            'city_id.exists' => 'A megadott település nem létezik'
        ];
    }
}