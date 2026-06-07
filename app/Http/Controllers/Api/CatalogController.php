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

class CatalogController extends Controller
{
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

    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('active', true)
            ->with(['category', 'brand', 'images', 'variants.stock'])
            ->firstOrFail();

        return new ProductDetailResource($product);
    }

    public function categories()
    {
        return CategoryResource::collection(
            Category::where('active', true)->orderBy('name')->get()
        );
    }

    public function brands()
    {
        return BrandResource::collection(
            Brand::where('active', true)->orderBy('name')->get()
        );
    }
}