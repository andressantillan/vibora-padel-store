<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id'    => ['required', 'exists:brands,id'],
            'name'        => ['required', 'string', 'max:200', "unique:products,name,{$productId}"],
            'slug'        => ['required', 'string', 'max:200', "unique:products,slug,{$productId}"],
            'description' => ['nullable', 'string'],
            'active'      => ['boolean'],

            // Imágenes
            'images'           => ['nullable', 'array'],
            'images.*'         => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'main_image_index' => ['nullable', 'integer'],
            'delete_images'    => ['nullable', 'array'],
            'delete_images.*'  => ['exists:product_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'brand_id.required'    => 'La marca es obligatoria.',
            'name.required'        => 'El nombre del producto es obligatorio.',
            'name.unique'          => 'Ya existe un producto con ese nombre.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug'   => Str::slug($this->name),
            'active' => $this->boolean('active'),
        ]);
    }
}