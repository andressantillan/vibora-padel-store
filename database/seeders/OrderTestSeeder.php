<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscar o crear un cliente de prueba
        $user = User::firstOrCreate(
            ['email' => 'cliente.prueba@test.com'],
            [
                'name'     => 'Cliente de Prueba',
                'password' => Hash::make('password'),
            ]
        );

        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'dni'        => 35123456,
                'phone'      => '2804 555123',
                'birth_date' => '1990-05-15',
            ]
        );

        // 2. Crear una dirección para el cliente
        $address = Address::firstOrCreate(
            ['customer_id' => $customer->id, 'street' => 'Av. Fontana 250'],
            [
                'city'        => 'Trelew',
                'province'    => 'Chubut',
                'postal_code' => '9100',
                'is_default'  => true,
            ]
        );

        // 3. Tomar las variantes que existan en la base
        $variants = ProductVariant::whereHas('product')->with('product')->take(2)->get();

        if ($variants->isEmpty()) {
            $this->command->warn('No hay variantes cargadas. Creá al menos una variante antes de correr este seeder.');
            return;
        }

        // 4. Calcular totales
        $subtotal = 0;
        foreach ($variants as $variant) {
            $subtotal += $variant->price * 1; // cantidad 1 por variante
        }

        $discount = 0;
        $total    = $subtotal - $discount;

        // 5. Crear el pedido
        $order = Order::create([
            'customer_id' => $customer->id,
            'address_id'  => $address->id,
            'status'      => 'pendiente',
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'total'       => $total,
            'coupon_code' => null,
        ]);

        // 6. Crear los items del pedido
        foreach ($variants as $variant) {
            $order->items()->create([
                'product_variant_id' => $variant->id,
                'quantity'           => 1,
                'unit_price'         => $variant->price,
            ]);
        }

        // 7. Registrar el estado inicial en el historial
        $order->statusHistory()->create([
            'status' => 'pendiente',
            'notes'  => 'Pedido creado (seed de prueba).',
        ]);

        // 8. Crear un pago pendiente
        Payment::create([
            'order_id'  => $order->id,
            'method'    => 'transferencia',
            'amount'    => $total,
            'status'    => 'pendiente',
            'reference' => 'TEST-' . strtoupper(uniqid()),
        ]);

        $this->command->info("Pedido de prueba #{$order->id} creado correctamente con {$variants->count()} item(s).");
    }
}