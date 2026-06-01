<div class="d-flex justify-content-end gap-1">
    <button type="button"
            class="btn btn-sm btn-outline-secondary edit-variant-btn"
            data-id="{{ $variant->id }}"
            data-price="{{ $variant->price }}"
            data-color="{{ $variant->color }}"
            data-size="{{ $variant->size }}"
            data-weight="{{ $variant->weight }}"
            data-quantity="{{ $variant->stock->quantity ?? 0 }}"
            data-min="{{ $variant->stock->min_quantity ?? 5 }}"
            data-bs-toggle="modal"
            data-bs-target="#modalEditVariant"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>

    <button type="button"
            class="btn btn-sm btn-outline-danger delete-variant-btn"
            data-id="{{ $variant->id }}"
            data-sku="{{ $variant->sku }}"
            data-bs-toggle="modal"
            data-bs-target="#modalDeleteVariant"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>