<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'        => ['required', 'exists:orders,id', 'unique:shipments,order_id'],
            'carrier'         => ['nullable', \Illuminate\Validation\Rule::in(array_keys(\App\Models\Shipment::CARRIERS))],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'status'          => ['required', 'in:en_preparacion,enviado'],
            'shipped_at'      => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.unique'  => 'Este pedido ya tiene un envío registrado.',
            'carrier.required' => 'La empresa de transporte es obligatoria.',
            'status.in'        => 'El estado del envío debe ser "en preparación" o "enviado".',
        ];
    }
}