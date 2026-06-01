@extends('layouts.app')

@section('title', 'Detalle de categoría')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $category->name }}</h1>
    @can('catalog.manage')
        <x-show-actions
        :edit-route="route('admin.categories.edit', $category)"
        :back-route="route('admin.categories.index')" />
    @else
        {{-- Solo el botón volver si no puede editar --}}
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endcan
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