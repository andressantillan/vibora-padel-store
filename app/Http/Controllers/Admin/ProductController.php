<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'mainImage'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $brands     = Brand::where('active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        $data    = $request->validated();
        $product = Product::create($data);

        $this->handleImages($request, $product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'variants.stock']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $brands     = Brand::where('active', true)->orderBy('name')->get();
        $product->load('images');

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        // Eliminar imágenes marcadas para borrar
        if ($request->delete_images) {
            $images = ProductImage::whereIn('id', $request->delete_images)->get();
            foreach ($images as $image) {
                Storage::disk('cloudinary')->delete($image->public_id);
                $image->delete();
            }
        }

        $this->handleImages($request, $product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        if ($product->variants()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'No se puede eliminar el producto porque tiene variantes asociadas.');
        }
    
        foreach ($product->images as $image) {
            Storage::disk('cloudinary')->delete($image->public_id);
            $image->delete();
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    private function handleImages(StoreProductRequest|UpdateProductRequest $request, Product $product): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $mainIndex     = $request->input('main_image_index', 0);
        $currentSort   = $product->images()->max('sort') ?? 0;
        $hasMainImage  = $product->images()->where('is_main', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            
            $path = $file->store('products', 'cloudinary');
            $url = Storage::disk('cloudinary')->url($path);
            $publicId = $path;

            $isMain = !$hasMainImage && $index === (int) $mainIndex;

            ProductImage::create([
                'product_id' => $product->id,
                'url'        => $url,
                'public_id'  => $publicId,
                'is_main'    => $isMain,
                'sort'       => ++$currentSort,
            ]);

            if ($isMain) {
                $hasMainImage = true;
            }
        }
    }
}