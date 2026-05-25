<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderStatusManager
{
    /**
     * Cambio interno de estado + efectos de stock + historial.
     * Es privado al flujo: solo lo llaman los métodos sync* y cancel.
     */
    protected function transition(Order $order, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $order->status;

        if ($oldStatus === $newStatus) {
            return;
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus, $notes) {
            if ($newStatus === 'pagado' && !$this->stockYaDescontado($oldStatus)) {
                $this->descontarStock($order);
            }

            if ($newStatus === 'cancelado') {
                if ($this->stockYaDescontado($oldStatus)) {
                    $this->reponerStock($order);
                }
                // Marca los pagos no rechazados como rechazados
                $order->payments()
                ->where('status', 'pendiente')
                ->update(['status' => 'rechazado']);
            }

            $order->update(['status' => $newStatus]);

            $order->statusHistory()->create([
                'status' => $newStatus,
                'notes'  => $notes,
            ]);
        });
    }

    /** Disparado al aprobar un pago. */
    public function syncFromPayment(Order $order): void
    {
        if ($order->status === 'pendiente') {
            $this->transition($order, 'pagado', 'Pago aprobado — estado actualizado automáticamente.');
        }
    }

    /** Disparado al cambiar el estado de un envío. */
    public function syncFromShipment(Shipment $shipment): void
    {
        $order = $shipment->order;

        $map = [
            'en_transito' => 'enviado',
            'entregado'   => 'entregado',
        ];

        if (!isset($map[$shipment->status])) {
            return; // estado "pendiente" del envío no cambia el pedido
        }

        $target = $map[$shipment->status];

        // No retrocede ni pisa un pedido cancelado
        if ($order->status === 'cancelado') {
            return;
        }

        if ($target === 'enviado' && in_array($order->status, ['pagado'])) {
            $this->transition($order, 'enviado', 'Envío en tránsito — estado actualizado automáticamente.');
        }

        if ($target === 'entregado' && in_array($order->status, ['pagado', 'enviado'])) {
            $this->transition($order, 'entregado', 'Envío entregado — estado actualizado automáticamente.');
        }
    }

    /** Única acción manual: cancelar. */
    public function cancel(Order $order, ?string $notes = null): ?string
    {
        if ($order->status === 'cancelado') {
            return 'El pedido ya está cancelado.';
        }

        if ($order->status === 'entregado') {
            return 'No se puede cancelar un pedido ya entregado.';
        }

        $this->transition($order, 'cancelado', $notes ?? 'Pedido cancelado manualmente.');

        return null;
    }

    private function stockYaDescontado(string $status): bool
    {
        return in_array($status, ['pagado', 'enviado', 'entregado']);
    }

    private function descontarStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_variant_id', $item->product_variant_id)->lockForUpdate()->first();
            if ($stock) {
                $stock->decrement('quantity', $item->quantity);
            }
        }
    }

    private function reponerStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_variant_id', $item->product_variant_id)->lockForUpdate()->first();
            if ($stock) {
                $stock->increment('quantity', $item->quantity);
            }
        }
    }
}