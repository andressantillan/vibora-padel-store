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
            'carrier'         => ['required', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'status'          => ['required', 'in:pendiente,en_transito,entregado'],
            'shipped_at'      => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'carrier.required' => 'La empresa de transporte es obligatoria.',
        ];
    }
}