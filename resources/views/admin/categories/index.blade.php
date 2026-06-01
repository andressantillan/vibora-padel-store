@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Categorías</h1>
    @can('catalog.manage')
        <x-new-button :route="route('admin.categories.create')" label="Nueva categoría" />
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

<x-filter-bar :action="route('admin.categories.index')">
    <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nombre de la categoría">
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
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th style="width:100px">Estado</th>
                    <th style="width:140px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="text-muted">{{ $category->id }}</td>
                    <td>
                        <a href="{{ route('admin.categories.show', $category) }}" class="text-decoration-none">
                            {{ $category->name }}
                        </a>
                    </td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>
                        @if($category->active)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-end gap-2">
                        <x-row-actions
                            :show-route="route('admin.categories.show', $category)"
                            :edit-route="route('admin.categories.edit', $category)"
                            :delete-route="route('admin.categories.destroy', $category)"
                            item-name="la categoría {{ $category->name }}" 
                            :can-edit="auth()->user()->can('catalog.manage')"
                            :can-delete="auth()->user()->can('catalog.manage')" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay categorías cargadas todavía.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($categories->hasPages())
    <div class="mt-3">{{ $categories->links() }}</div>
@endif

@endsection