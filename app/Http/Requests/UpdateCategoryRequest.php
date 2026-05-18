<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id;

        return [
            'name'        => ['required', 'string', 'max:100', "unique:categories,name,{$categoryId}"],
            'slug'        => ['required', 'string', 'max:100', "unique:categories,slug,{$categoryId}"],
            'description' => ['nullable', 'string'],
            'active'      => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'El nombre de la categoría es obligatorio.',
            'name.unique'      => 'Ya existe una categoría con ese nombre.',
            'slug.required'    => 'El slug de la categoría es obligatorio.',
            'slug.unique'      => 'Ya existe una categoría con ese slug.',
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