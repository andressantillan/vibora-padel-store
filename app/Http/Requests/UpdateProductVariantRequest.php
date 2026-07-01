<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantId = $this->route('variant')->id;

        return [
            'price'        => ['required', 'numeric', 'min:0'],
            'color'        => ['nullable', 'string', 'max:50'],
            'size'         => ['nullable', 'string', 'max:50'],
            'weight'       => ['nullable', 'integer', 'gt:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required'        => 'El precio es obligatorio.',
            'price.min'             => 'El precio no puede ser negativo.',
            'quantity.required'     => 'La cantidad es obligatoria.',
            'quantity.min'          => 'La cantidad no puede ser negativa.',
            'min_quantity.required' => 'La cantidad mínima es obligatoria.',
            'min_quantity.min'      => 'La cantidad mínima no puede ser negativa.',
            'weight.gt'             => 'El peso debe ser mayor a 0.',
            'weight.integer'        => 'El peso debe ser un número entero (sin decimales).',
        ];
    }
}