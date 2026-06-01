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
        protected PaymentValidator $validator,
        protected OrderStatusManager $statusManager,
    ) {}

    public function store(StorePaymentRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        $error = $this->validator->validateStore($order, (float) $request->amount);
        if ($error) {
            return redirect()->route('admin.orders.show', $order->id)->with('error', $error);
        }

        $data = $request->validated();
        $data['paid_at'] = $request->filled('paid_at')
            ? Carbon::parse($request->paid_at)->setTimeFrom(now())
            : null;

        DB::transaction(function () use ($data, $order) {
            $payment = Payment::create($data);

            // si el pago se aprueba, el pedido pasa a "pagado"
            if ($payment->status === 'aprobado') {
                $this->statusManager->syncFromPayment($order);
            }
        });

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $error = $this->validator->validateUpdate($payment, (float) $request->amount);
        if ($error) {
            return redirect()->route('admin.orders.show', $payment->order_id)->with('error', $error);
        }

        $data = $request->validated();
        $data['paid_at'] = $request->filled('paid_at')
            ? Carbon::parse($request->paid_at)->setTimeFrom(now())
            : null;

        DB::transaction(function () use ($data, $payment) {
            $wasApproved = $payment->status === 'aprobado';
            $payment->update($data);

            // Si pasó a aprobado (y antes no lo estaba), sincroniza el pedido
            if (!$wasApproved && $payment->status === 'aprobado') {
                $this->statusManager->syncFromPayment($payment->order->fresh());
            }
        });

        return redirect()->route('admin.orders.show', $payment->order_id)
            ->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(Payment $payment)
    {
        $error = $this->validator->validateDelete($payment);
        if ($error) {
            return redirect()->route('admin.orders.show', $payment->order_id)->with('error', $error);
        }

        $orderId = $payment->order_id;
        $payment->delete();

        return redirect()->route('admin.orders.show', $orderId)
            ->with('success', 'Pago eliminado correctamente.');
    }
}