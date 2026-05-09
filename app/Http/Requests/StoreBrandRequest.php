<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
            'slug'   => ['required', 'string', 'max:100', 'unique:brands,slug'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la marca es obligatorio.',
            'name.unique'   => 'Ya existe una marca con ese nombre.',
            'logo.image'    => 'El logo debe ser una imagen.',
            'logo.max'      => 'El logo no puede superar 2MB.',
            'slug.required' => 'El slug de la marca es obligatorio.',
            'slug.unique'   => 'Ya existe una marca con ese slug.',
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