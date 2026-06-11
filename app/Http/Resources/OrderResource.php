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
        ];
    }
}
