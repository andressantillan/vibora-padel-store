@extends('layouts.app')

@section('title', 'Detalle de producto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $product->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-secondary">Editar</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">← Volver</a>
    </div>
</div>

<div class="row g-4">
    {{-- Datos principales --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">Datos del producto</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8">{{ $product->name }}</dd>

                    <dt class="col-sm-4">Slug</dt>
                    <dd class="col-sm-8"><code>{{ $product->slug }}</code></dd>

                    <dt class="col-sm-4">Categoría</dt>
                    <dd class="col-sm-8">{{ $product->category->name }}</dd>

                    <dt class="col-sm-4">Marca</dt>
                    <dd class="col-sm-8">{{ $product->brand->name }}</dd>

                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8">{{ $product->description ?? '—' }}</dd>

                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8">
                        @if($product->active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Creado</dt>
                    <dd class="col-sm-8">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Imágenes --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                Imágenes
                <span class="badge bg-secondary ms-1">{{ $product->images->count() }}</span>
            </div>
            <div class="card-body">
                @if($product->images->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->images as $image)
                            <div class="position-relative">
                                <img src="{{ $image->url }}"
                                     alt="Imagen {{ $loop->iteration }}"
                                     class="rounded border {{ $image->is_main ? 'border-primary border-2' : '' }}"
                                     style="width:80px;height:80px;object-fit:contain">
                                @if($image->is_main)
                                    <span class="position-absolute top-0 start-0 badge bg-primary"
                                          style="font-size:0.6rem">Principal</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No hay imágenes cargadas.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Variantes --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    Variantes
                    <span class="badge bg-secondary ms-1">{{ $product->variants->count() }}</span>
                </span>
                <a href="#" class="btn btn-sm btn-outline-primary">+ Agregar variante</a>
            </div>
            <div class="card-body p-0">
                @if($product->variants->isNotEmpty())
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>SKU</th>
                                <th>Color</th>
                                <th>Talle</th>
                                <th>Peso</th>
                                <th>Precio</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                            <tr>
                                <td><code>{{ $variant->sku }}</code></td>
                                <td>{{ $variant->color ?? '—' }}</td>
                                <td>{{ $variant->size ?? '—' }}</td>
                                <td>{{ $variant->weight ? $variant->weight . ' kg' : '—' }}</td>
                                <td>${{ number_format($variant->price, 2) }}</td>
                                <td>
                                    @if($variant->stock)
                                        <span class="badge {{ $variant->stock->quantity <= $variant->stock->min_quantity ? 'bg-danger' : 'bg-success' }}">
                                            {{ $variant->stock->quantity }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted p-3 mb-0">Este producto no tiene variantes todavía.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection