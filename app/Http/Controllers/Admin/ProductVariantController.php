<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    public function store(StoreProductVariantRequest $request)
    {
        $data    = $request->validated();
        $variant = ProductVariant::create($data);

        // Crear stock automáticamente
        $variant->stock()->create([
            'quantity'     => $data['quantity'],
            'min_quantity' => $data['min_quantity'],
        ]);

        return redirect()
            ->route('admin.products.show', $data['product_id'])
            ->with('success', 'Variante creada correctamente.');
    }

    public function update(UpdateProductVariantRequest $request, ProductVariant $variant)
    {
        $data = $request->validated();
        $variant->update($data);

        // Actualizar stock
        $variant->stock()->updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'quantity'     => $data['quantity'],
                'min_quantity' => $data['min_quantity'],
            ]
        );

        return redirect()
            ->route('admin.products.show', $variant->product_id)
            ->with('success', 'Variante actualizada correctamente.');
    }

    public function destroy(ProductVariant $variant)
    {
        $productId = $variant->product_id;

        if($variant->stock()->count() > 0) {
            return redirect()
                ->route('admin.products.show', $productId)
                ->with('error', 'No se puede eliminar la variante porque hay stock existente.');
        }

        $variant->stock()->delete();
        $variant->delete();

        return redirect()
            ->route('admin.products.show', $productId)
            ->with('success', 'Variante eliminada correctamente.');
    }
}