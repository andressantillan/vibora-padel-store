@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Productos</h1>
    <x-new-button :route="route('admin.products.create')" label="Nuevo producto" />
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<x-filter-bar :action="route('admin.products.index')">
    {{-- Búsqueda --}}
    <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nombre del producto">
    </div>

    {{-- Categoría --}}
    <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Categoría</label>
        <select name="category_id" class="form-select">
            <option value="">Todas</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Marca --}}
    <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Marca</label>
        <select name="brand_id" class="form-select">
            <option value="">Todas</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}"
                    {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Estado --}}
    <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Estado</label>
        <select name="active" class="form-select">
            <option value="">Todos</option>
            <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
</x-filter-bar>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:70px">Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th style="width:100px">Estado</th>
                    <th style="width:140px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="text-muted">{{ $product->id }}</td>
                    <td>
                        @if($product->mainImage)
                            <img src="{{ $product->mainImage->url }}"
                                 alt="{{ $product->name }}"
                                 class="rounded"
                                 style="width:40px;height:40px;object-fit:contain">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->brand->name }}</td>
                    <td>
                        @if($product->active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-end gap-2">
                        <x-row-actions
                            :show-route="route('admin.products.show', $product)"
                            :edit-route="route('admin.products.edit', $product)"
                            :delete-route="route('admin.products.destroy', $product)"
                            item-name="el producto {{ $product->name }}" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay productos cargados todavía.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($products->hasPages())
    <div class="mt-3">{{ $products->links() }}</div>
@endif
@endsection