@extends('layouts.app')

@section('title', 'Nueva categoría')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nueva categoría</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        ← Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            @include('admin.categories._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar categoría</button>
            </div>
        </form>
    </div>
</div>
@endsection