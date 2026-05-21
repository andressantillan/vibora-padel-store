<div class="d-flex justify-content-end gap-1">
    <button type="button"
            class="btn btn-sm btn-outline-secondary edit-address-btn"
            data-id="{{ $address->id }}"
            data-street="{{ $address->street }}"
            data-city="{{ $address->city }}"
            data-province="{{ $address->province }}"
            data-postal="{{ $address->postal_code }}"
            data-default="{{ $address->is_default ? 1 : 0 }}"
            data-bs-toggle="modal"
            data-bs-target="#modalEditAddress"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>

    <button type="button"
            class="btn btn-sm btn-outline-danger delete-address-btn"
            data-id="{{ $address->id }}"
            data-street="{{ $address->street }}"
            data-bs-toggle="modal"
            data-bs-target="#modalDeleteAddress"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>