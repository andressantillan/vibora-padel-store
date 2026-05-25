<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'  => ['required', 'exists:orders,id'],
            'method'    => ['required', 'in:efectivo,transferencia,debito,credito'],
            'amount'    => ['required', 'numeric', 'min:0'],
            'status'    => ['required', 'in:pendiente,aprobado,rechazado'],
            'reference' => ['nullable', 'string', 'max:100'],
            'paid_at'   => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' => 'El método de pago es obligatorio.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.min'      => 'El monto no puede ser negativo.'
        ];
    }
}