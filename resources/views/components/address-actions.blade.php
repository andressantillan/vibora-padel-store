<div class="d-flex justify-content-end gap-1">
    <a href="{{ route('admin.addresses.edit', $address) }}"
       class="btn btn-sm btn-outline-secondary" title="Editar">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-sm btn-outline-danger delete-address-btn"
            data-id="{{ $address->id }}" data-street="{{ $address->street }}"
            data-bs-toggle="modal" data-bs-target="#modalDeleteAddress" title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>