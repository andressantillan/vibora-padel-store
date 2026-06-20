@extends('layouts.app')

@section('title', 'Editar variante')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Editar variante</h1>
        <p class="mb-0">
            Producto: <span class="fw-semibold">{{ $product->name }}</span>
            · SKU: <code>{{ $variant->sku }}</code>
        </p>
    </div>
    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver al producto
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.variants.update', $variant) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.products.variants._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar variante</button>
            </div>
        </form>
    </div>
</div>
@endsection