@extends('layouts.app')

@section('title', "Pedido #{$order->id}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        Pedido #{{ $order->id }}
        <span class="badge bg-{{ $order->statusColor() }} ms-2">{{ $order->statusLabel() }}</span>
    </h1>
    @if($order->code)
        <div class="mt-2 mb-2">
            <i class="bi bi-upc-scan me-1 text-secondary"></i> <span class="text-secondary fw-semibold me-1">Código de seguimiento:</span> 
            <span class="badge bg-primary fs-6 shadow-sm">{{ $order->code }}</span>
        </div>
    @endif
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>


<div class="row g-4">
    {{-- Columna principal --}}
    <div class="col-lg-8">
        {{-- Productos --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Productos</div>
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead>
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
                    <tfoot>
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
                                    {{ \App\Models\Order::STATUS_LABELS[$history->status] ?? $history->status }}
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
        {{-- Estado del pedido (dos dimensiones) --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">Estado del pedido</div>
            <div class="card-body">
                <dl class="row mb-3 small">
                    <dt class="col-5">Código de seguimiento del pedido</dt>
                    <dd class="col-7">
                        <code>{{ $order->code ?? '—' }}</code>
                    </dd>

                    <dt class="col-5">Pago</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $order->payment_status === 'pagado' ? 'success' : 'warning' }}">
                            {{ $order->paymentStatusLabel() }}
                        </span>
                    </dd>

                    <dt class="col-5">Logística</dt>
                    <dd class="col-7">
                        <span class="badge bg-info">{{ $order->fulfillmentStatusLabel() }}</span>
                    </dd>

                    <dt class="col-5">General</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
                    </dd>
                </dl>

                <p class="small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    El estado avanza automáticamente al registrar el pago y el envío.
                </p>

                @can('orders.manage')
                    @if($order->canBeCancelled())
                        <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                data-bs-toggle="modal" data-bs-target="#modalCancelOrder">
                            <i class="bi bi-x-circle me-1"></i> Cancelar pedido
                        </button>
                    @endif
                @endcan
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
                @can('shipments.manage')
                    @if(!$order->shipment && $order->payment_status === 'pagado')
                        <button type="button" class="btn btn-sm btn-success"
                                data-bs-toggle="modal" data-bs-target="#modalShipment">
                            <i class="bi bi-plus-lg me-1"></i> Registrar
                        </button>
                    @elseif($order->shipment)
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#modalEditShipment">
                            <i class="bi bi-pencil"></i>
                        </button>
                    @endif
                @endcan
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
                    <p class="mb-1 small"><span class="fw-semibold">Transporte:</span> {{ $order->shipment->carrierLabel() }}</p>
                    <p class="mb-0 small">
                        <span class="fw-semibold">Seguimiento:</span>
                        {{ $order->shipment->tracking_number ?? '—' }}
                    </p>
                @elseif($order->payment_status !== 'pagado')
                    <p class="text-muted small mb-0">El envío estará disponible una vez registrado el pago.</p>
                @else
                    <p class="text-muted small mb-0">Pendiente de registrar el envío.</p>
                @endif
            </div>
        </div>

        {{-- Pago --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Pago</span>
                @can('payments.manage')
                    @if($order->payment_status !== 'pagado' && $order->status !== 'cancelado')
                        <button type="button" class="btn btn-sm btn-success"
                                data-bs-toggle="modal" data-bs-target="#modalPayment">
                            <i class="bi bi-plus-lg me-1"></i> Registrar
                        </button>
                    @endif
                @endcan
            </div>
            <div class="card-body p-0">
                @if($order->payments->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($order->payments as $payment)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block fw-semibold">{{ ucfirst($payment->method) }}</span>
                                    <small class="text-muted">
                                        Pagado el {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                                    </small>
                                </div>
                                <span class="fw-semibold">${{ number_format($payment->amount, 2) }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted p-3 mb-0">Sin pago registrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== MODALES ===== --}}

{{-- Cancelar pedido --}}
@can('orders.manage')
@if($order->canBeCancelled())
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
                    <p class="small text-muted mb-3">
                        Esta acción es definitiva. Solo se pueden cancelar pedidos pendientes de pago.
                    </p>
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
@endif
@endcan

{{-- Registrar envío --}}
@can('shipments.manage')
@if(!$order->shipment && $order->payment_status === 'pagado')
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
@elseif($order->shipment)
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
@endcan

{{-- Registrar pago --}}
@can('payments.manage')
@if($order->payment_status !== 'pagado' && $order->status !== 'cancelado')
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
                    @include('admin.orders._payment_form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endcan

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if(old('order_id') && old('method'))
            new bootstrap.Modal(document.getElementById('modalPayment')).show();
        @elseif(old('tracking_number') !== null || old('carrier') !== null)
            @if($order->shipment)
                new bootstrap.Modal(document.getElementById('modalEditShipment')).show();
            @else
                new bootstrap.Modal(document.getElementById('modalShipment')).show();
            @endif
        @endif
    });
</script>
@endif
@endsection