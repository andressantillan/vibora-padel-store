
@extends('layouts.app')

@section('title', 'Marcas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Marcas</h1>
    @can('catalog.manage')
        <x-new-button :route="route('admin.brands.create')" label="Nueva marca" />
    @endcan
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<x-filter-bar :action="route('admin.brands.index')">
    <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nombre de la marca">
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Estado</label>
        <select name="active" class="form-select">
            <option value="">Todos</option>
            <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Activa</option>
            <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactiva</option>
        </select>
    </div>
</x-filter-bar>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:70px">Logo</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th style="width:100px">Estado</th>
                    <th style="width:140px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td class="text-muted">{{ $brand->id }}</td>
                    <td>
                        @if($brand->logo_url)
                            <img src="{{ $brand->logo_url }}"
                                 alt="{{ $brand->name }}"
                                 class="rounded"
                                 style="width:40px;height:40px;object-fit:contain">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.brands.show', $brand) }}" class="text-decoration-none">
                            {{ $brand->name }}
                        </a>
                    </td>
                    <td><code>{{ $brand->slug }}</code></td>
                    <td>
                        @if($brand->active)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-end gap-2">
                        <x-row-actions
                            :show-route="route('admin.brands.show', $brand)"
                            :edit-route="route('admin.brands.edit', $brand)"
                            :delete-route="route('admin.brands.destroy', $brand)"
                            item-name="la marca {{ $brand->name }}"
                            :can-edit="auth()->user()->can('catalog.manage')"
                            :can-delete="auth()->user()->can('catalog.manage')" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No hay marcas cargadas todavía.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($brands->hasPages())
    <div class="mt-3">{{ $brands->links() }}</div>
@endif
@endsection