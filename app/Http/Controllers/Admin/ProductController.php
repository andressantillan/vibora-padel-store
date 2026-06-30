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
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'mainImage']);

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por marca
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filtro por estado
        if ($request->filled('active')) {
            $query->where('active', $request->active === '1');
        }

        $products = $query->orderBy('name')
            ->paginate(20)
            ->withQueryString(); // conserva los filtros en la paginación

        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    
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

        $mainImageStr = $request->input('main_image');
        if ($mainImageStr && str_starts_with($mainImageStr, 'existing_')) {
            $existingId = str_replace('existing_', '', $mainImageStr);
            if (!in_array($existingId, $request->delete_images ?? [])) {
                $product->images()->update(['is_main' => false]);
                $product->images()->where('id', $existingId)->update(['is_main' => true]);
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

        $mainImageStr = $request->input('main_image', 'new_0');
        $isNewMain = str_starts_with($mainImageStr, 'new_');
        $mainIndex = $isNewMain ? (int) str_replace('new_', '', $mainImageStr) : -1;

        if ($isNewMain) {
            $product->images()->update(['is_main' => false]);
        }

        $currentSort   = $product->images()->max('sort') ?? 0;
        $hasMainImage  = $product->images()->where('is_main', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            
            $path = $file->store('products', 'cloudinary');
            $url = Storage::disk('cloudinary')->url($path);
            $publicId = $path;

            $isMain = !$hasMainImage && $index === $mainIndex;

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