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
            'sku'          => ['required', 'string', 'max:100', "unique:product_variants,sku,{$variantId}"],
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
            'sku.required'   => 'El SKU es obligatorio.',
            'sku.unique'     => 'Ya existe una variante con ese SKU.',
            'price.required' => 'El precio es obligatorio.',
        ];
    }
}