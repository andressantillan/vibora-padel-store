@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nuevo producto</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        ← Volver
    </a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form')
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Guardar producto</button>
    </div>
</form>
@endsection