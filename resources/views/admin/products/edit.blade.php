@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Editar: {{ $product->name }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        ← Volver
    </a>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.products._form')
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Actualizar producto</button>
    </div>
</form>
@endsection