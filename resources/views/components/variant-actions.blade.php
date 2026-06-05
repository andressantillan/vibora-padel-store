<div class="d-flex justify-content-end gap-1">
    <a href="{{ route('admin.variants.edit', $variant) }}"
       class="btn btn-sm btn-outline-secondary" title="Editar">
        <i class="bi bi-pencil"></i>
    </a>

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