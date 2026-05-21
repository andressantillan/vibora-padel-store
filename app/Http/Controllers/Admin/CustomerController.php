<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('user')
            ->withCount('orders')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Customer::create([
                'user_id'    => $user->id,
                'dni'        => $data['dni']        ?? null,
                'phone'      => $data['phone']      ?? null,
                'birth_date' => $data['birth_date'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['user', 'addresses', 'orders']);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer->load('user');

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $customer) {
            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $customer->user->update($userData);

            $customer->update([
                'dni'        => $data['dni']        ?? null,
                'phone'      => $data['phone']      ?? null,
                'birth_date' => $data['birth_date'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return redirect()
                ->route('admin.customers.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados.');
        }

        if ($customer->addresses()->exists()) {
            return redirect()
                ->route('admin.customers.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene direcciones asociadas.');
        }

        DB::transaction(function () use ($customer) {
            $user = $customer->user;
            $customer->addresses()->delete();
            $customer->delete();
            $user->delete();
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}