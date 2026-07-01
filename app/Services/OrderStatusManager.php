<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderStatusManager
{
    protected function refreshStatus(Order $order, ?string $notes = null): void
    {
        $newStatus = $order->deriveStatus();
        if ($order->status === $newStatus) return;

        $order->update(['status' => $newStatus]);
        $order->statusHistory()->create(['status' => $newStatus, 'notes' => $notes]);
    }

    /** Al registrar el pago: descuenta stock y pasa fulfillment a "en preparación". */
    public function onPaymentRegistered(Order $order, ?string $notes = null): void
    {
        DB::transaction(function () use ($order, $notes) {
            if ($order->payment_status !== 'pagado') {
                $order->update([
                    'payment_status'     => 'pagado',
                    'fulfillment_status' => 'en_preparacion',
                ]);
                $this->descontarStock($order);
                $this->refreshStatus($order, $notes ?? 'Pago registrado — preparando envío.');
            }
        });
    }

    /** Al despachar el envío. */
    public function onShipmentSent(Shipment $shipment): void
    {
        $order = $shipment->order;
        DB::transaction(function () use ($order, $shipment) {
            if ($shipment->status === 'enviado') {
                $order->update(['fulfillment_status' => 'enviado']);
                $this->refreshStatus($order, 'Pedido despachado.');
            }
        });
    }

    /** Cancelación manual: solo si está pendiente (impago). */
    public function cancel(Order $order, ?string $notes = null): ?string
    {
        if ($order->status !== 'pendiente') {
            return 'Solo se pueden cancelar pedidos pendientes de pago.';
        }

        $order->update(['status' => 'cancelado']);
        $order->statusHistory()->create([
            'status' => 'cancelado',
            'notes'  => $notes ?? 'Pedido cancelado manualmente.',
        ]);

        return null;
    }

    private function descontarStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_variant_id', $item->product_variant_id)->lockForUpdate()->first();
            if ($stock) $stock->decrement('quantity', $item->quantity);
        }
    }
}