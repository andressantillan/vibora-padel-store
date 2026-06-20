<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 *
 * Endpoints para registro, inicio de sesión, cierre de sesión y perfil del usuario.
 */
class AuthController extends Controller
{
    /**
     * Registrar usuario
     *
     * Crea un nuevo usuario y su perfil de cliente.
     *
     * @bodyParam name string required Nombre del usuario. Example: Juan Pérez
     * @bodyParam email string required Email del usuario. Example: juan@mail.com
     * @bodyParam password string required Contraseña del usuario. Example: password123
     * @bodyParam password_confirmation string required Confirmación de la contraseña. Example: password123
     * @bodyParam phone string Teléfono del usuario. Example: 2804555123
     * @bodyParam dni integer DNI del usuario. Example: 35123456
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'dni'      => ['nullable', 'integer'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Customer::create([
                'user_id' => $user->id,
                'phone'   => $data['phone'] ?? null,
                'dni'     => $data['dni'] ?? null,
            ]);

            return $user;
        });

        $token = $user->createToken('storefront')->plainTextToken;

        return response()->json([
            'user'  => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'token' => $token,
        ], 201);
    }

    /**
     * Iniciar sesión
     *
     * Verifica las credenciales del usuario y devuelve un token de acceso.
     *
     * @bodyParam email string required Email del usuario. Example: juan@mail.com
     * @bodyParam password string required Contraseña del usuario. Example: password123
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        // Solo clientes pueden usar la API de la tienda
        if (!$user->customer) {
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta no tiene acceso a la tienda.'],
            ]);
        }

        $token = $user->createToken('storefront')->plainTextToken;

        return response()->json([
            'user'  => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'token' => $token,
        ]);
    }

    /**
     * Cerrar sesión
     *
     * Revoca el token de acceso del usuario.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    /**
     * Perfil del usuario
     *
     * Devuelve la información del usuario autenticado.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('customer.addresses');

        return response()->json([
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'phone'     => $user->customer->phone,
            'addresses' => $user->customer->addresses,
        ]);
    }
}