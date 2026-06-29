<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group Catálogo
 *
 * Endpoints públicos para explorar el catálogo de la tienda.
 */
class CatalogController extends Controller
{
    /**
     * Listar productos
     *
     * Devuelve los productos activos con paginación. Permite filtrar por categoría, marca y búsqueda.
     *
     * @queryParam category string Slug de la categoría. Example: paletas
     * @queryParam brand string Slug de la marca. Example: bullpadel
     * @queryParam search string Búsqueda por nombre. Example: pala
     */
    public function products(Request $request)
    {
        $query = Product::where('active', true)
            ->with(['category', 'brand', 'mainImage', 'variants']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('brand')) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }
        if ($request->filled('search')) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        return ProductResource::collection($query->paginate(12));
    }

    /**
     * Productos destacados
     *
     * Devuelve 3 productos aleatorios activos. Para garantizar alto rendimiento,
     * los resultados se almacenan en caché por 10 minutos.
     */
    public function featuredProducts()
    {
        $products = \Illuminate\Support\Facades\Cache::remember('featured_products', 600, function () {
            return Product::where('active', true)
                ->with(['category', 'brand', 'mainImage', 'variants'])
                ->inRandomOrder()
                ->limit(3)
                ->get();
        });

        return ProductResource::collection($products);
    }

    /**
     * Detalle de producto
     *
     * Devuelve un producto por su slug, con todas sus variantes y stock disponible.
     *
     * @urlParam slug string required El slug del producto. Example: pala-bullpadel-vertex
     */
    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('active', true)
            ->with(['category', 'brand', 'images', 'variants.stock'])
            ->firstOrFail();

        return new ProductDetailResource($product);
    }

    /**
     * Listar categorías
     *
     * Devuelve las categorías activas ordenadas por nombre.
     */
    public function categories()
    {
        return CategoryResource::collection(
            Category::where('active', true)->orderBy('name')->get()
        );
    }

    /**
     * Listar marcas
     *
     * Devuelve las marcas activas ordenadas por nombre.
     */
    public function brands()
    {
        return BrandResource::collection(
            Brand::where('active', true)->orderBy('name')->get()
        );
    }
}