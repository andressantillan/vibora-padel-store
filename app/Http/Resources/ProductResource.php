<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category'    => $this->whenLoaded('category', fn() => $this->category->name),
            'brand'       => $this->whenLoaded('brand', fn() => $this->brand->name),
            'shape'       => $this->shape,
            'level'       => $this->level,
            'image'       => $this->whenLoaded('mainImage', fn() => $this->mainImage?->url),
            'price_from'  => $this->whenLoaded('variants', fn() => $this->variants->min('price')),
        ];
    }
}
