<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

/**
 * @group Tienda
 */
class OrderController extends Controller
{

    /**
     * Crear pedido
     *
     * Crea un pedido desde la tienda con los datos del invitado, dirección e items.
     * Si el email ya existe, reutiliza la cuenta. El pedido nace en estado "pendiente".
     *
     * @bodyParam customer.name string required Nombre del cliente. Example: Juan Pérez
     * @bodyParam customer.email string required Email del cliente. Example: juan@mail.com
     * @bodyParam customer.phone string required Teléfono. Example: 2804555123
     * @bodyParam customer.dni integer required DNI. Example: 35123456
     * @bodyParam address.street string required Calle y número. Example: Av. Fontana 250
     * @bodyParam address.city string required Ciudad. Example: Trelew
     * @bodyParam address.province string required Provincia. Example: Chubut
     * @bodyParam address.postal_code string required Código postal. Example: 9100
     * @bodyParam items array required Lista de items del pedido.
     * @bodyParam items[].variant_id integer required ID de la variante. Example: 1
     * @bodyParam items[].quantity integer required Cantidad. Example: 2
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Datos del invitado
            'customer.name'  => ['required', 'string', 'max:255'],
            'customer.email' => ['required', 'email', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:20'],
            'customer.dni'   => ['required', 'integer'],

            // Dirección de envío
            'address.street'      => ['required', 'string', 'max:255'],
            'address.city'        => ['required', 'string', 'max:100'],
            'address.province'    => ['required', 'string', 'max:100'],
            'address.postal_code' => ['required', 'string', 'max:20'],

            // Items
            'items'              => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($data) {
            // 1. Reutilizar o crear el User (cuenta automática, contraseña no usable)
            $user = User::firstOrCreate(
                ['email' => $data['customer']['email']],
                [
                    'name'     => $data['customer']['name'],
                    'password' => Hash::make(Str::random(40)),
                ]
            );

            // Si ya existía, actualizamos el nombre por si cambió
            $user->update(['name' => $data['customer']['name']]);

            // 2. Reutilizar o crear el Customer, actualizando datos de contacto
            $customer = Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'dni'   => $data['customer']['dni'],
                    'phone' => $data['customer']['phone'],
                ]
            );

            // 3. Crear la dirección para este pedido
            $address = Address::create([
                'customer_id' => $customer->id,
                'street'      => $data['address']['street'],
                'city'        => $data['address']['city'],
                'province'    => $data['address']['province'],
                'postal_code' => $data['address']['postal_code'],
                'is_default'  => false,
            ]);

            // 4. Validar stock y calcular totales
            $subtotal = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                $variant = ProductVariant::with('stock')->lockForUpdate()->findOrFail($item['variant_id']);

                $available = $variant->stock?->quantity ?? 0;
                if ($available < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuficiente para {$variant->sku}. Disponible: {$available}."],
                    ]);
                }

                $subtotal += $variant->price * $item['quantity'];
                $lines[] = [
                    'product_variant_id' => $variant->id,
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $variant->price,
                ];
            }

            // 5. Crear el pedido
            $order = Order::create([
                'customer_id'        => $customer->id,
                'address_id'         => $address->id,
                'status'             => 'pendiente',
                'payment_status'     => 'pendiente',
                'fulfillment_status' => 'sin_preparar',
                'subtotal'           => $subtotal,
                'discount'           => 0,
                'total'              => $subtotal,
            ]);

            // 6. Crear los items
            foreach ($lines as $line) {
                $order->items()->create($line);
            }

            // 7. Registrar el estado inicial
            $order->statusHistory()->create([
                'status' => 'pendiente',
                'notes'  => 'Pedido creado desde la tienda.',
            ]);

            return $order;
        });

        $order->load('items.variant.product', 'address', 'customer.user');

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    /**
     * Listar pedidos
     *
     * Devuelve una lista paginada de los pedidos del cliente autenticado.
     */
    public function index(Request $request)
    {
        $customer = $request->user()->customer;

        $orders = Order::where('customer_id', $customer->id)
            ->with('items.variant.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Mostrar pedido
     *
     * Devuelve los detalles de un pedido específico del cliente autenticado.
     *
     * @urlParam order int required El ID del pedido. Example: 1
     */
    public function show(Request $request, Order $order)
    {
        
        if ($order->customer_id !== $request->user()->customer->id) {
            abort(403, 'No autorizado.');
        }

        $order->load('items.variant.product');

        return new OrderResource($order);
    }

    /*public function store(Request $request)
    {
        $data = $request->validate([
            'address_id'        => ['required', 'exists:addresses,id'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.variant_id'=> ['required', 'exists:product_variants,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1'],
        ]);

        $customer = $request->user()->customer;

        // La dirección debe pertenecer al cliente
        if (!$customer->addresses()->where('id', $data['address_id'])->exists()) {
            throw ValidationException::withMessages([
                'address_id' => ['La dirección no pertenece al cliente.'],
            ]);
        }

        $order = DB::transaction(function () use ($data, $customer) {
            $subtotal = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                $variant = ProductVariant::with('stock')->lockForUpdate()->findOrFail($item['variant_id']);

                // Validar stock disponible
                $available = $variant->stock?->quantity ?? 0;
                if ($available < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuficiente para {$variant->sku}. Disponible: {$available}."],
                    ]);
                }

                $lineTotal = $variant->price * $item['quantity'];
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_variant_id' => $variant->id,
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $variant->price,
                ];
            }

            $order = Order::create([
                'customer_id'        => $customer->id,
                'address_id'         => $data['address_id'],
                'status'             => 'pendiente',
                'payment_status'     => 'pendiente',
                'fulfillment_status' => 'sin_preparar',
                'subtotal'           => $subtotal,
                'discount'           => 0,
                'total'              => $subtotal,
            ]);

            foreach ($lines as $line) {
                $order->items()->create($line);
            }

            $order->statusHistory()->create([
                'status' => 'pendiente',
                'notes'  => 'Pedido creado desde la tienda.',
            ]);

            return $order;
        });

        $order->load('items.variant.product');

        return (new OrderResource($order))->response()->setStatusCode(201);
    }*/
}