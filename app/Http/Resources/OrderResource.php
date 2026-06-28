<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'status'     => $this->statusLabel(),
            'subtotal'   => (float) $this->subtotal,
            'discount'   => (float) $this->discount,
            'total'      => (float) $this->total,
            'created_at' => $this->created_at->toIso8601String(),
            'items'      => $this->whenLoaded('items', fn() => $this->items->map(fn($item) => [
                'product'    => $item->variant->product->name ?? null,
                'sku'        => $item->variant->sku ?? null,
                'quantity'   => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal'   => (float) $item->subtotal(),
            ])),
            'address' => $this->whenLoaded('address', fn() => [
                'street'      => $this->address->street,
                'city'        => $this->address->city,
                'province'    => $this->address->province,
                'postal_code' => $this->address->postal_code,
            ]),
            'shipment' => $this->whenLoaded('shipment', fn() => [
                'status'         => $this->shipment->statusLabel(),
                'carrier'        => $this->shipment->carrier,
                'tracking_number'=> $this->shipment->tracking_number,
                'shipped_at'     => $this->shipment->shipped_at?->toIso8601String(),
                'delivered_at'   => $this->shipment->delivered_at?->toIso8601String(),
            ]),
            'payments' => $this->whenLoaded('payments', fn() => $this->payments->map(fn($payment) => [
                'method'    => $payment->methodLabel(),
                'amount'    => (float) $payment->amount,
                'status'    => $payment->statusLabel(),
                'reference' => $payment->reference,
                'paid_at'   => $payment->paid_at?->toIso8601String(),
            ])),
        ];
    }
}
