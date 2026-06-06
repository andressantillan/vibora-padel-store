<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function create(Customer $customer)
    {
        return view('admin.customers.addresses.create', compact('customer'));
    }

    public function store(StoreAddressRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $data['customer_id'] = $customer->id;

        DB::transaction(function () use ($data, $customer) {
            if ($data['is_default']) {
                $customer->addresses()->update(['is_default' => false]);
            }
            Address::create($data);
        });

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Dirección agregada correctamente.');
    }

    public function edit(Address $address)
    {
        $address->load('customer.user');
        $customer = $address->customer;

        return view('admin.customers.addresses.edit', compact('address', 'customer'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $address) {
            if ($data['is_default']) {
                Address::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
            $address->update($data);
        });

        return redirect()
            ->route('admin.customers.show', $address->customer_id)
            ->with('success', 'Dirección actualizada correctamente.');
    }

    public function destroy(Address $address)
    {
        $customerId = $address->customer_id;
        $address->delete();

        return redirect()
            ->route('admin.customers.show', $customerId)
            ->with('success', 'Dirección eliminada correctamente.');
    }
}