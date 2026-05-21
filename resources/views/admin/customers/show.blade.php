@extends('layouts.app')

@section('title', 'Detalle de cliente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $customer->user->name }}</h1>
    <x-show-actions
        :edit-route="route('admin.customers.edit', $customer)"
        :back-route="route('admin.customers.index')" />
</div>

<div class="row g-4">
    {{-- Datos del cliente --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold">Datos del cliente</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8">{{ $customer->user->name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $customer->user->email }}</dd>

                    <dt class="col-sm-4">DNI</dt>
                    <dd class="col-sm-8">{{ $customer->dni ?? '—' }}</dd>

                    <dt class="col-sm-4">Teléfono</dt>
                    <dd class="col-sm-8">{{ $customer->phone ?? '—' }}</dd>

                    <dt class="col-sm-4">Nacimiento</dt>
                    <dd class="col-sm-8">
                        {{ $customer->birth_date ? $customer->birth_date->format('d/m/Y') : '—' }}
                    </dd>

                    <dt class="col-sm-4">Registrado</dt>
                    <dd class="col-sm-8">{{ $customer->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Direcciones --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    Direcciones
                    <span class="badge bg-secondary ms-1">{{ $customer->addresses->count() }}</span>
                </span>
                <button type="button" class="btn btn-sm btn-success"
                        data-bs-toggle="modal" data-bs-target="#modalAddress">
                    <i class="bi bi-plus-lg me-1"></i> Agregar
                </button>
            </div>
            <div class="card-body p-0">
                @if($customer->addresses->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($customer->addresses as $address)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    {{ $address->street }}<br>
                                    <small class="text-muted">
                                        {{ $address->city }}, {{ $address->province }} ({{ $address->postal_code }})
                                    </small>
                                    @if($address->is_default)
                                        <span class="badge bg-primary ms-1">Predeterminada</span>
                                    @endif
                                </div>
                                <x-address-actions :address="$address" />
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted p-3 mb-0">Este cliente no tiene direcciones cargadas.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Pedidos --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">
                Pedidos
                <span class="badge bg-secondary ms-1">{{ $customer->orders->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($customer->orders->isNotEmpty())
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted p-3 mb-0">Este cliente no tiene pedidos todavía.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal crear dirección --}}
<div class="modal fade" id="modalAddress" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.addresses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.customers._address_form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal editar dirección (único) --}}
<div class="modal fade" id="modalEditAddress" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editAddressForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Calle y número</label>
                        <input type="text" name="street" id="edit_street" class="form-control">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="city" id="edit_city" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Provincia</label>
                            <input type="text" name="province" id="edit_province" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código postal</label>
                        <input type="text" name="postal_code" id="edit_postal_code" class="form-control">
                    </div>
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_default" value="0">
                        <input type="checkbox" name="is_default" value="1" id="edit_is_default" class="form-check-input">
                        <label for="edit_is_default" class="form-check-label">Marcar como predeterminada</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar dirección (único) --}}
<div class="modal fade" id="modalDeleteAddress" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteAddressForm" method="POST">
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
                    <p class="mb-0">¿Eliminar la dirección <strong id="delete_address_label"></strong>?</p>
                    <p class="text-muted small mt-2 mb-0">Esta acción no se puede deshacer.</p>
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
// Editar dirección
document.querySelectorAll('.edit-address-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('editAddressForm');
        form.action = `/admin/addresses/${btn.dataset.id}`;

        document.getElementById('edit_street').value       = btn.dataset.street;
        document.getElementById('edit_city').value         = btn.dataset.city;
        document.getElementById('edit_province').value     = btn.dataset.province;
        document.getElementById('edit_postal_code').value  = btn.dataset.postal;
        document.getElementById('edit_is_default').checked = btn.dataset.default === '1';
    });
});

// Eliminar dirección
document.querySelectorAll('.delete-address-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('deleteAddressForm');
        form.action = `/admin/addresses/${btn.dataset.id}`;
        document.getElementById('delete_address_label').textContent = btn.dataset.street;
    });
});
</script>

@endsection