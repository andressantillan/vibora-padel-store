<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\SkuGenerator;

class ProductVariantController extends Controller
{

    public function __construct(protected SkuGenerator $skuGenerator){}

    public function create(Product $product)
    {
        return view('admin.products.variants.create', compact('product'));
    }

    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['product_id'] = $product->id;
        $data['sku'] = $this->skuGenerator->generate($product, $data);

        $variant = ProductVariant::create($data);

        $variant->stock()->create([
            'quantity'     => $data['quantity'],
            'min_quantity' => $data['min_quantity'],
        ]);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', "Variante creada correctamente. SKU: {$data['sku']}");
    }

    public function edit(ProductVariant $variant)
    {
        $variant->load('product', 'stock');
        $product = $variant->product;

        return view('admin.products.variants.edit', compact('variant', 'product'));
    }

    public function update(UpdateProductVariantRequest $request, ProductVariant $variant)
    {
        $data = $request->validated();
        
        $variant->load('product');
        if ($this->shouldRegenerateSku($variant, $data)) {
            $data['sku'] = $this->skuGenerator->generate($variant->product, $data);
        }

        $variant->update($data);

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

        $variant->load('stock');

        // No permitir eliminar variantes vinculadas a pedidos
        if (\App\Models\OrderItem::where('product_variant_id', $variant->id)->exists()) {
            return redirect()
                ->route('admin.products.show', $variant->product_id)
                ->with('error', "No se puede eliminar la variante {$variant->sku} porque está vinculada a uno o más pedidos históricos.");
        }

        // No permitir eliminar variantes con stock vigente
        if ($variant->stock && $variant->stock->quantity > 0) {
            return redirect()
                ->route('admin.products.show', $variant->product_id)
                ->with('error', "No se puede eliminar la variante {$variant->sku} porque tiene stock vigente ({$variant->stock->quantity} unidades).");
        }

        $productId = $variant->product_id;
        $variant->stock()->delete();
        $variant->delete();

        return redirect()
            ->route('admin.products.show', $productId)
            ->with('success', 'Variante eliminada correctamente.');
    }

    private function shouldRegenerateSku(ProductVariant $variant, array $newData): bool
    {
        return $variant->color  != ($newData['color']  ?? null)
            || $variant->size   != ($newData['size']   ?? null)
            || $variant->weight != ($newData['weight'] ?? null);
    }


}