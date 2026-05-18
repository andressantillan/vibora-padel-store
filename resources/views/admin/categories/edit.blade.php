@extends('layouts.app')

@section('title', 'Editar categoría')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Editar categoría: {{ $category->name }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        ← Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar categoría</button>
            </div>
        </form>
    </div>
</div>
@endsection