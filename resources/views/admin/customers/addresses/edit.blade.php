@extends('layouts.app')

@section('title', 'Editar dirección')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Editar dirección</h1>
        <p class="text-muted mb-0">
            Cliente: <span class="fw-semibold">{{ $customer->user->name }}</span>
        </p>
    </div>
    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver al cliente
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.addresses.update', $address) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.customers.addresses._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar dirección</button>
            </div>
        </form>
    </div>
</div>
@endsection