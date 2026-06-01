<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class SkuGenerator
{
    public function generate(Product $product, array $variantData): string
    {
        $brand   = $this->normalize($product->brand->name, 4);
        $name    = $this->initials($product->name);
        $color   = !empty($variantData['color']) ? $this->normalize($variantData['color'], 3) : null;
        $size    = !empty($variantData['size'])  ? $this->normalize($variantData['size'], 3)  : null;
        $weight  = !empty($variantData['weight']) ? str_replace('.', '', $variantData['weight']) : null;

        $parts = array_filter([$brand, $name, $color, $size, $weight]);
        $base  = implode('-', $parts);

        return $this->ensureUnique($base);
    }

    private function normalize(string $value, int $length): string
    {
        $clean = Str::ascii($value);
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $clean);
        return strtoupper(substr($clean, 0, $length));
    }

    private function initials(string $name): string
    {
        $words = explode(' ', Str::ascii($name));
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return preg_replace('/[^A-Z0-9]/', '', $initials);
    }

    private function ensureUnique(string $base): string
    {
        $sku     = $base;
        $counter = 1;

        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $base . '-' . $counter;
            $counter++;
        }

        return $sku;
    }
}