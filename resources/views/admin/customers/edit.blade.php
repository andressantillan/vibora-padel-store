@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Editar cliente: {{ $customer->user->name }}</h1>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.customers._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar cliente</button>
            </div>
        </form>
    </div>
</div>
@endsection