<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id'    => ['required', 'exists:brands,id'],
            'name'        => ['required', 'string', 'max:200', 'unique:products,name'],
            'slug'        => ['required', 'string', 'max:200', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'active'      => ['boolean'],

            // Imágenes
            'images'          => ['nullable', 'array'],
            'images.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'main_image_index' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no existe.',
            'brand_id.required'    => 'La marca es obligatoria.',
            'brand_id.exists'      => 'La marca seleccionada no existe.',
            'name.required'        => 'El nombre del producto es obligatorio.',
            'name.unique'          => 'Ya existe un producto con ese nombre.',
            'images.*.image'       => 'Cada archivo debe ser una imagen.',
            'images.*.max'         => 'Cada imagen no puede superar 2MB.',
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