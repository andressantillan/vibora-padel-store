<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderStatusManager;
use App\Services\PaymentValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        protected OrderStatusManager $statusManager,
    ) {}

    public function store(StorePaymentRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->payment_status === 'pagado') {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'Este pedido ya tiene el pago registrado.');
        }
        
        if ($order->status === 'cancelado') {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'No se puede registrar el pago de un pedido cancelado.');
        }

        DB::transaction(function () use ($request, $order) {
            Payment::create([
                'order_id' => $order->id,
                'method'   => $request->method,
                'amount'   => $order->total,
                'status'   => 'pagado',
                'paid_at'  => Carbon::parse($request->paid_at)->setTimeFrom(now()),
            ]);
            $this->statusManager->onPaymentRegistered($order);
        });

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Pago registrado correctamente.');
    }

    
}