@extends('layouts.app')

@section('title', "Pedido #{$order->id}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        Pedido #{{ $order->id }}
        <span class="badge bg-{{ $order->statusColor() }} ms-2">{{ $order->statusLabel() }}</span>
    </h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

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

<div class="row g-4">
    {{-- Columna principal --}}
    <div class="col-lg-8">
        {{-- Productos --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Productos</div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->variant->product->name ?? 'Producto eliminado' }}</td>
                            <td><code>{{ $item->variant->sku ?? '—' }}</code></td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">${{ number_format($item->subtotal(), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end">Subtotal</td>
                            <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="4" class="text-end text-success">
                                Descuento {{ $order->coupon_code ? "($order->coupon_code)" : '' }}
                            </td>
                            <td class="text-end text-success">-${{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Historial de estados --}}
        <div class="card">
            <div class="card-header fw-semibold">Historial de estados</div>
            <div class="card-body">
                @if($order->statusHistory->isNotEmpty())
                    <ul class="list-unstyled mb-0">
                        @foreach($order->statusHistory as $history)
                        <li class="d-flex gap-3 pb-3 ps-3 ms-2 {{ !$loop->last ? 'border-start' : '' }}">
                            <div>
                                <span class="badge bg-{{ \App\Models\Order::STATUS_COLORS[$history->status] ?? 'secondary' }}">
                                    {{ \App\Models\Order::STATUSES[$history->status] ?? $history->status }}
                                </span>
                                <small class="text-muted ms-2">{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                @if($history->notes)
                                    <p class="small text-muted mb-0 mt-1">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">Sin cambios de estado registrados.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Columna lateral --}}
    <div class="col-lg-4">
        {{-- Estado del pedido --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Estado del pedido</div>
            <div class="card-body">
                <p class="mb-3">
                    Estado actual:
                    <span class="badge bg-{{ $order->statusColor() }} ms-1">{{ $order->statusLabel() }}</span>
                </p>
                <p class="small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    El estado avanza automáticamente al aprobar el pago y al actualizar el envío.
                </p>
                @if(!in_array($order->status, ['cancelado', 'entregado']))
                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                            data-bs-toggle="modal" data-bs-target="#modalCancelOrder">
                        <i class="bi bi-x-circle me-1"></i> Cancelar pedido
                    </button>
                @endif
            </div>
        </div>

        {{-- Cliente --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Cliente</div>
            <div class="card-body">
                <p class="mb-1 fw-semibold">{{ $order->customer->user->name }}</p>
                <p class="mb-1 small text-muted">{{ $order->customer->user->email }}</p>
                @if($order->customer->phone)
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-telephone me-1"></i>{{ $order->customer->phone }}
                    </p>
                @endif
                <a href="{{ route('admin.customers.show', $order->customer) }}"
                   class="btn btn-sm btn-outline-secondary mt-2">Ver cliente</a>
            </div>
        </div>

        {{-- Envío --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Envío</span>
                @if(!$order->shipment && in_array($order->status, ['pagado', 'enviado']))
                    <button type="button" class="btn btn-sm btn-success"
                            data-bs-toggle="modal" data-bs-target="#modalShipment">
                        <i class="bi bi-plus-lg me-1"></i> Registrar
                    </button>
                @elseif($order->shipment && $order->status !== 'cancelado')
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#modalEditShipment">
                        <i class="bi bi-pencil"></i>
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($order->address)
                    <p class="mb-1">{{ $order->address->street }}</p>
                    <p class="mb-2 small text-muted">
                        {{ $order->address->city }}, {{ $order->address->province }}
                        ({{ $order->address->postal_code }})
                    </p>
                @else
                    <p class="text-muted mb-2">Sin dirección asignada.</p>
                @endif

                @if($order->shipment)
                    <hr>
                    <p class="mb-1 small">
                        <span class="fw-semibold">Estado:</span>
                        <span class="badge bg-{{ $order->shipment->statusColor() }}">
                            {{ $order->shipment->statusLabel() }}
                        </span>
                    </p>
                    <p class="mb-1 small"><span class="fw-semibold">Transporte:</span> {{ $order->shipment->carrier }}</p>
                    <p class="mb-0 small">
                        <span class="fw-semibold">Seguimiento:</span>
                        {{ $order->shipment->tracking_number ?? '—' }}
                    </p>
                @else
                    <p class="text-muted small mb-0">Este pedido aún no tiene envío registrado.</p>
                @endif
            </div>
        </div>

        {{-- Pagos --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Pagos</span>
                @if($order->payments->isEmpty() && !in_array($order->status, ['enviado', 'entregado', 'cancelado']))
                    <button type="button" class="btn btn-sm btn-success"
                            data-bs-toggle="modal" data-bs-target="#modalPayment">
                        <i class="bi bi-plus-lg me-1"></i> Registrar
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
                @if($order->payments->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($order->payments as $payment)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="d-block fw-semibold">{{ $payment->methodLabel() }}</span>
                                    <small>
                                        <span class="badge bg-{{ $payment->statusColor() }}">
                                            {{ $payment->statusLabel() }}
                                        </span>
                                        @if($payment->reference)
                                            <span class="text-muted">· {{ $payment->reference }}</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-semibold d-block">${{ number_format($payment->amount, 2) }}</span>
                                    @if(!in_array($order->status, ['enviado', 'entregado', 'cancelado']))
                                    <div class="d-flex gap-1 mt-1">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary edit-payment-btn"
                                                data-id="{{ $payment->id }}"
                                                data-method="{{ $payment->method }}"
                                                data-amount="{{ $payment->amount }}"
                                                data-status="{{ $payment->status }}"
                                                data-reference="{{ $payment->reference }}"
                                                data-paid="{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '' }}"
                                                data-bs-toggle="modal" data-bs-target="#modalEditPayment"
                                                title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-payment-btn"
                                                data-id="{{ $payment->id }}"
                                                data-bs-toggle="modal" data-bs-target="#modalDeletePayment"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted p-3 mb-0">Sin pagos registrados.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== MODALES ===== --}}

{{-- Cancelar pedido --}}
<div class="modal fade" id="modalCancelOrder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Cancelar pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que querés cancelar el pedido #{{ $order->id }}?</p>
                    @if(in_array($order->status, ['pagado', 'enviado']))
                        <p class="small text-warning mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            El stock descontado será repuesto automáticamente.
                        </p>
                    @endif
                    <label class="form-label fw-semibold">Motivo (opcional)</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Motivo de la cancelación..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Confirmar cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Registrar envío --}}
@if(!$order->shipment)
<div class="modal fade" id="modalShipment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.shipments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.orders._shipment_form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar envío</button>
                </div>
            </form>
        </div>
    </div>
</div>
@else
{{-- Editar envío --}}
<div class="modal fade" id="modalEditShipment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.shipments.update', $order->shipment) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.orders._shipment_form', ['shipment' => $order->shipment])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar envío</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Registrar pago --}}
<div class="modal fade" id="modalPayment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.orders._payment_form', ['orderTotal' => $order->total])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Editar pago --}}
<div class="modal fade" id="modalEditPayment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPaymentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Método</label>
                        <select name="method" id="edit_method" class="form-select">
                            @foreach(\App\Models\Payment::METHODS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="status" id="edit_status" class="form-select">
                            @foreach(\App\Models\Payment::STATUSES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Referencia</label>
                        <input type="text" name="reference" id="edit_reference" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de pago</label>
                        <input type="date" name="paid_at" id="edit_paid_at" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Eliminar pago --}}
<div class="modal fade" id="modalDeletePayment" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deletePaymentForm" method="POST">
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
                    <p class="mb-0">¿Eliminar este pago?</p>
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
// Editar pago
document.querySelectorAll('.edit-payment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('editPaymentForm');
        form.action = `/admin/payments/${btn.dataset.id}`;
        document.getElementById('edit_method').value    = btn.dataset.method;
        document.getElementById('edit_amount').value     = btn.dataset.amount;
        document.getElementById('edit_status').value     = btn.dataset.status;
        document.getElementById('edit_reference').value  = btn.dataset.reference;
        document.getElementById('edit_paid_at').value    = btn.dataset.paid;
    });
});

// Eliminar pago
document.querySelectorAll('.delete-payment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('deletePaymentForm');
        form.action = `/admin/payments/${btn.dataset.id}`;
    });
});
</script>
@endsection