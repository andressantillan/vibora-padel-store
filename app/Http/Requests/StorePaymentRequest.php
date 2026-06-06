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
            'order_id' => ['required', 'exists:orders,id'],
            'method'   => ['required', 'in:efectivo,transferencia,debito,credito'],
            'paid_at'  => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' => 'El método de pago es obligatorio.',
            'paid_at.required' => 'La fecha de pago es obligatoria.',
            'paid_at.date' => 'La fecha de pago no es válida.',
            'paid_at.before_or_equal' => 'La fecha de pago no puede ser futura.',
            'method.in' => 'El método de pago seleccionado no es válido.',
        ];
    }
}