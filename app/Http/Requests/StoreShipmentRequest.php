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
            'carrier'         => ['required', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'status'          => ['required', 'in:pendiente,en_transito,entregado'],
            'shipped_at'      => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.unique'  => 'Este pedido ya tiene un envío registrado.',
            'carrier.required' => 'La empresa de transporte es obligatoria.',
        ];
    }
}