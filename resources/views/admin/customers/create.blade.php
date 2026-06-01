@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nuevo cliente</h1>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            @include('admin.customers._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar cliente</button>
            </div>
        </form>
    </div>
</div>
@endsection