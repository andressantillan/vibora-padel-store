@extends('layouts.app')

@section('title', 'Agregar variante')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Agregar variante</h1>
        <p class="mb-0">
            Producto: <span class="fw-semibold">{{ $product->name }}</span>
        </p>
    </div>
    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver al producto
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST">
            @csrf
            @include('admin.products.variants._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar variante</button>
            </div>
        </form>
    </div>
</div>
@endsection