@extends('layouts.app')

@section('title', 'Envíos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Envíos</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filtros --}}
<x-filter-bar :action="route('admin.shipments.index')">
    <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Cliente o tracking">
    </div>
    <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Estado</label>
        <select name="status" class="form-select">
            <option value="">Todos</option> 
            @foreach(\App\Models\Shipment::STATUSES as $value => $label)
                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Transporte</label>
        <input type="text" name="carrier" value="{{ request('carrier') }}"
               class="form-control" placeholder="Ej: OCA">
    </div>
</x-filter-bar>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:90px">Pedido</th>
                    <th>Cliente</th>
                    <th>Transporte</th>
                    <th>Tracking</th>
                    <th>Estado</th>
                    <th>Enviado</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($shipments as $shipment)
                <tr>
                    <td class="fw-semibold">#{{ $shipment->order_id }}</td>
                    <td>{{ $shipment->order->customer->user->name }}</td>
                    <td>{{ $shipment->carrier }}</td>
                    <td>
                        @if($shipment->tracking_number)
                            <code>{{ $shipment->tracking_number }}</code>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $shipment->statusColor() }}">
                            {{ $shipment->statusLabel() }}
                        </span>
                    </td>
                    <td>{{ $shipment->shipped_at ? $shipment->shipped_at->format('d/m/Y') : '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.orders.show', $shipment->order_id) }}"
                           class="btn btn-sm btn-outline-info" title="Ver pedido">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay envíos que coincidan con la búsqueda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$shipments" />
@endsection