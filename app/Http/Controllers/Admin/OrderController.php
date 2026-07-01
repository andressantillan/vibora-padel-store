<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusManager;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderStatusManager $statusManager) {}
    

    public function index(Request $request)
    {
        $query = Order::with('customer.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer.user', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'customer.user', 'address', 'items.variant.product',
            'statusHistory', 'shipment', 'payments',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $error = $this->statusManager->cancel($order, $request->notes);

        if ($error) {
            return redirect()->route('admin.orders.show', $order)->with('error', $error);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Pedido cancelado correctamente.');
    }
}