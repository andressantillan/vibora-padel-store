@extends('layouts.app')

@section('title', 'Detalle de categoría')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $category->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-secondary">
            Editar
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            ← Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Datos de la categoría</div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9">{{ $category->name }}</dd>

            <dt class="col-sm-3">Slug</dt>
            <dd class="col-sm-9"><code>{{ $category->slug }}</code></dd>

            <dt class="col-sm-3">Descripción</dt>
            <dd class="col-sm-9">{{ $category->description ?? '—' }}</dd>

            <dt class="col-sm-3">Estado</dt>
            <dd class="col-sm-9">
                @if($category->active)
                    <span class="badge bg-success">Activa</span>
                @else
                    <span class="badge bg-secondary">Inactiva</span>
                @endif
            </dd>

            <dt class="col-sm-3">Creada</dt>
            <dd class="col-sm-9">{{ $category->created_at->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Actualizada</dt>
            <dd class="col-sm-9">{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection