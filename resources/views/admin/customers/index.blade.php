@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Clientes</h1>
    @can('customers.manage')
        <x-new-button :route="route('admin.customers.create')" label="Nuevo cliente" />
    @endcan
</div>



<x-filter-bar :action="route('admin.customers.index')">
    <div class="col-md-5">
        <label class="form-label small fw-semibold mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nombre, email o DNI">
    </div>
</x-filter-bar>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>Teléfono</th>
                    <th style="width:90px">Pedidos</th>
                    <th style="width:160px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="text-muted">{{ $customer->id }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-decoration-none">
                            {{ $customer->user->name }}
                        </a>
                    </td>
                    <td>{{ $customer->user->email }}</td>
                    <td>{{ $customer->dni ?? '—' }}</td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td class="text-center">
                        @if($customer->orders_count > 0)
                            <span class="badge bg-info text-dark">{{ $customer->orders_count }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-row-actions
                            :show-route="route('admin.customers.show', $customer)"
                            :edit-route="route('admin.customers.edit', $customer)"
                            :delete-route="route('admin.customers.destroy', $customer)"
                            item-name="el cliente {{ $customer->user->name }}"
                            :can-edit="auth()->user()->can('customers.manage')"
                            :can-delete="auth()->user()->can('customers.manage')" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay clientes cargados todavía.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$customers" />
@endsection