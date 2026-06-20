@extends('layouts.app')

@section('title', 'Detalle de cliente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $customer->user->name }}</h1>
    @can('users.manage')
        <x-show-actions
            :edit-route="route('admin.customers.edit', $customer)"
            :back-route="route('admin.customers.index')" />
    @else
        {{-- Solo el botón volver si no puede editar --}}
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endcan
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
                @can('customers.manage')
                    <a href="{{ route('admin.customers.addresses.create', $customer) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Agregar
                    </a>
                @endcan
            </div>
            <div class="card-body p-0 scrollable-list">
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
            <div class="card-body p-0 scrollable-list">
                @if($customer->orders->isNotEmpty())
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
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