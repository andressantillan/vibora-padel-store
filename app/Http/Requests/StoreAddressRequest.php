<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'street'      => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'province'    => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'is_default'  => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'street.required'      => 'La calle es obligatoria.',
            'city.required'        => 'La ciudad es obligatoria.',
            'province.required'    => 'La provincia es obligatoria.',
            'postal_code.required' => 'El código postal es obligatorio.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
        ]);
    }
}