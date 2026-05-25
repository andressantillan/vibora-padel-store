<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;

class PaymentValidator
{
    const LOCKED_ORDER_STATUSES = ['enviado', 'entregado', 'cancelado'];

    public function validateStore(Order $order, float $amount): ?string
    {
        if ($error = $this->checkLockedOrder($order)) {
            return $error;
        }

        if ($order->payments()->exists()) {
            return 'Este pedido ya tiene un pago registrado. No se permite más de un pago por pedido.';
        }

        if ($amount > $order->total) {
            return 'El monto del pago no puede superar el total del pedido ($' . number_format($order->total, 2) . ').';
        }

        return null;
    }

    public function validateUpdate(Payment $payment, float $amount): ?string
    {
        if ($error = $this->checkLockedOrder($payment->order)) {
            return $error;
        }

        if ($amount > $payment->order->total) {
            return 'El monto del pago no puede superar el total del pedido ($' . number_format($payment->order->total, 2) . ').';
        }

        return null;
    }

    public function validateDelete(Payment $payment): ?string
    {
        if ($error = $this->checkLockedOrder($payment->order)) {
            return $error;
        }

        if ($payment->status === 'aprobado') {
            return 'No se puede eliminar un pago aprobado. Primero cambialo a otro estado.';
        }

        return null;
    }

    private function checkLockedOrder(Order $order): ?string
    {
        if (in_array($order->status, self::LOCKED_ORDER_STATUSES)) {
            $label = Order::STATUSES[$order->status] ?? $order->status;
            return "No se pueden gestionar pagos de un pedido en estado \"{$label}\".";
        }

        return null;
    }
}