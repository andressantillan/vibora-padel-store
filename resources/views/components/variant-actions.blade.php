<div class="d-flex justify-content-end gap-1">
    <button type="button"
            class="btn btn-sm btn-outline-secondary"
            data-bs-toggle="modal"
            data-bs-target="#modalEditVariant{{ $variant->id }}"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>

    <button type="button"
            class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#modalDeleteVariant{{ $variant->id }}"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="modalDeleteVariant{{ $variant->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Confirmar eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    ¿Estás seguro que querés eliminar la variante <strong>{{ $variant->sku }}</strong>?
                </p>
                <p class="text-muted small mt-2 mb-0">
                    Se eliminará también su stock asociado. Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <form action="{{ route('admin.variants.destroy', $variant) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>