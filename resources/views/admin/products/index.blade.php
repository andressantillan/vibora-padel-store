@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Productos</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        + Nuevo producto
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                    <td class="text-end">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
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