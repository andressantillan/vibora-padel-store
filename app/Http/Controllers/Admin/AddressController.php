<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // Si esta dirección es predeterminada, quitamos el flag de las demás
            if ($data['is_default']) {
                Address::where('customer_id', $data['customer_id'])
                    ->update(['is_default' => false]);
            }

            Address::create($data);
        });

        return redirect()
            ->route('admin.customers.show', $data['customer_id'])
            ->with('success', 'Dirección agregada correctamente.');
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