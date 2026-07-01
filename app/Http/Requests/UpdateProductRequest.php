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
            'shape' => ['nullable', 'in:redonda,lagrima,diamante'],
            'level' => ['nullable', 'in:iniciacion,intermedio,avanzado'],

            // Imágenes
            'images'           => ['nullable', 'array'],
            'images.*'         => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:2048'],
            'main_image'       => ['nullable', 'string'],
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
            'images.*.image'       => 'Cada archivo debe ser una imagen.',
            'images.*.mimes'       => 'Las imágenes deben estar en formato jpg, jpeg, png, webp o avif.',
            'images.*.max'         => 'Cada imagen no puede superar 2MB.',
            'shape.in'             => 'La forma seleccionada no es válida.',
            'level.in'             => 'El nivel seleccionado no es válido.',
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