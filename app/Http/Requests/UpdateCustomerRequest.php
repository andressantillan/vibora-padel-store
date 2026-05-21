<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customer = $this->route('customer');
        $userId   = $customer->user_id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', "unique:users,email,{$userId}"],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            'dni'        => ['nullable', 'string', 'max:20', "unique:customers,dni,{$customer->id}"],
            'phone'      => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El email es obligatorio.',
            'email.unique'       => 'Ya existe un usuario con ese email.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'dni.unique'         => 'Ya existe un cliente con ese DNI.',
            'birth_date.before'  => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}