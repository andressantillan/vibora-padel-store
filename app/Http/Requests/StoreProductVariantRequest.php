<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'   => ['required', 'exists:products,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'color'        => ['nullable', 'string', 'max:50'],
            'size'         => ['nullable', 'string', 'max:50'],
            'weight'       => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required'    => 'El precio es obligatorio.',
            'price.min'         => 'El precio no puede ser negativo.',
            'quantity.required' => 'La cantidad es obligatoria.',
        ];
    }
}