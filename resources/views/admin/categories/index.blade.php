@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Categorías</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        + Nueva categoría
    </a>
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
                    <td class="text-end">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                        class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}"
                            method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
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