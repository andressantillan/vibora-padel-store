<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'category'    => $this->category->name,
            'brand'       => $this->brand->name,
            'shape'       => $this->shape,
            'level'       => $this->level,
            'images'      => $this->images->map(fn($img) => [
                'url'     => $img->url,
                'is_main' => $img->is_main,
            ]),
            'variants'    => $this->variants->map(fn($v) => [
                'id'        => $v->id,
                'sku'       => $v->sku,
                'color'     => $v->color,
                'size'      => $v->size,
                'weight'    => $v->weight,
                'price'     => (float) $v->price,
                'available' => $v->stock?->quantity ?? 0,
            ]),
        ];
    }
}
