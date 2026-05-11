<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'cloudinary');
            $data['logo_url']       = Storage::disk('cloudinary')->url($path);
            $data['logo_public_id'] = $path;
        }

        Brand::create($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marca creada correctamente.');
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Elimina el logo anterior si existe
            if ($brand->logo_public_id) {
                Storage::disk('cloudinary')->delete($brand->logo_public_id);
            }

            $path = $request->file('logo')->store('brands', 'cloudinary');
            $data['logo_url']       = Storage::disk('cloudinary')->url($path);
            $data['logo_public_id'] = $path;
        }

        $brand->update($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->logo_public_id) {
            Storage::disk('cloudinary')->delete($brand->logo_public_id);
        }

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marca eliminada correctamente.');
    }
}