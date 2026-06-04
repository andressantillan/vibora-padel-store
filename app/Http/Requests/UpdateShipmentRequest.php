<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'carrier'         => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'status'          => ['required', 'in:en_preparacion,enviado'],
            'shipped_at'      => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'carrier.required' => 'La empresa de transporte es obligatoria.',
            'status.in'        => 'El estado del envío debe ser "en preparación" o "enviado".',
        ];
    }
}