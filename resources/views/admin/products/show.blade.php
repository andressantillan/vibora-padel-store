@extends('layouts.app')

@section('title', 'Detalle de producto')

@section('content')


<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $product->name }}</h1>
    @can('catalog.manage')
        <x-show-actions
            :edit-route="route('admin.products.edit', $product)"
            :back-route="route('admin.products.index')" />
    @else
        {{-- Solo el botón volver si no puede editar --}}
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endcan
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

                    @if($product->shape || $product->level)
                        <dt class="col-sm-4">Forma</dt>
                        <dd class="col-sm-8">{{ \App\Models\Product::SHAPES[$product->shape] ?? '—' }}</dd>

                        <dt class="col-sm-4">Nivel</dt>
                        <dd class="col-sm-8">{{ \App\Models\Product::LEVELS[$product->level] ?? '—' }}</dd>
                    @endif

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
                @can('catalog.manage')
                    <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Agregar variante
                    </a>
                @endcan
            </div>
            <div class="card-body p-0">
                @if($product->variants->isNotEmpty())
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Color</th>
                                <th>Peso</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th style="width:120px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                            <tr>
                                <td><code>{{ $variant->sku }}</code></td>
                                <td>{{ $variant->color ?? '—' }}</td>
                                <td>{{ $variant->weight ? (int)$variant->weight . ' g' : '—' }}</td>
                                <td>${{ number_format($variant->price, 2) }}</td>
                                <td>
                                    @if($variant->stock)
                                        <span class="badge {{ $variant->stock->isLow() ? 'bg-danger' : 'bg-success' }}">
                                            {{ $variant->stock->quantity }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @can('catalog.manage')
                                    <x-variant-actions :variant="$variant" />
                                    @endcan
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

{{-- Modal eliminar variante (único) --}}
<div class="modal fade" id="modalDeleteVariant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteVariantForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">¿Eliminar la variante <strong id="delete_variant_label"></strong>?</p>
                    <p class="text-muted small mt-2 mb-0">Se eliminará también su stock asociado.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Eliminar variante
document.querySelectorAll('.delete-variant-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('deleteVariantForm');
        form.action = `/admin/variants/${btn.dataset.id}`;
        document.getElementById('delete_variant_label').textContent = btn.dataset.sku;
    });
});
</script>
@endsection