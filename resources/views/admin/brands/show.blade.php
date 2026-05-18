@extends('layouts.app')

@section('title', 'Detalle de marca')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $brand->name }}</h1>
    <x-show-actions
        :edit-route="route('admin.brands.edit', $brand)"
        :back-route="route('admin.brands.index')" />
</div>

<div class="row g-4">
    {{-- Datos principales --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">Datos de la marca</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8">{{ $brand->name }}</dd>

                    <dt class="col-sm-4">Slug</dt>
                    <dd class="col-sm-8"><code>{{ $brand->slug }}</code></dd>

                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8">
                        @if($brand->active)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Creada</dt>
                    <dd class="col-sm-8">{{ $brand->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4">Actualizada</dt>
                    <dd class="col-sm-8">{{ $brand->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Logo --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">Logo</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($brand->logo_url)
                    <img src="{{ $brand->logo_url }}"
                         alt="Logo de {{ $brand->name }}"
                         class="img-fluid rounded"
                         style="max-height: 160px; object-fit: contain;">
                @else
                    <p class="text-muted mb-0">Esta marca no tiene logo cargado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection