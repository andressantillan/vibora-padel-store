@extends('layouts.app')

@section('title', 'Detalle de producto')

@section('content')

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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $product->name }}</h1>
    <x-show-actions
        :edit-route="route('admin.products.edit', $product)"
        :back-route="route('admin.products.index')" />
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
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalVariant">
                    <i class="bi bi-plus-lg me-1"></i> Agregar variante
                </button>
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
                                <th style="width:120px"></th>
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
                                        <span class="badge {{ $variant->stock->isLow() ? 'bg-danger' : 'bg-success' }}">
                                            {{ $variant->stock->quantity }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-end gap-1">
                                    <x-variant-actions :variant="$variant" />
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
{{-- Modal crear variante --}}
<div class="modal fade" id="modalVariant" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.variants.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva variante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.products._variant_form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar variante</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal editar variante (único) --}}
<div class="modal fade" id="modalEditVariant" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editVariantForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar variante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Color</label>
                            <input type="text" name="color" id="edit_color" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Talle</label>
                            <input type="text" name="size" id="edit_size" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peso (kg)</label>
                        <input type="number" name="weight" id="edit_weight" class="form-control" step="0.01" min="0">
                    </div>
                    <hr>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stock actual</label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stock mínimo</label>
                            <input type="number" name="min_quantity" id="edit_min_quantity" class="form-control" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar variante</button>
                </div>
            </form>
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
// Editar variante
document.querySelectorAll('.edit-variant-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('editVariantForm');
        form.action = `/admin/variants/${btn.dataset.id}`;

        document.getElementById('edit_price').value        = btn.dataset.price;
        document.getElementById('edit_color').value        = btn.dataset.color;
        document.getElementById('edit_size').value         = btn.dataset.size;
        document.getElementById('edit_weight').value       = btn.dataset.weight;
        document.getElementById('edit_quantity').value     = btn.dataset.quantity;
        document.getElementById('edit_min_quantity').value = btn.dataset.min;
    });
});

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