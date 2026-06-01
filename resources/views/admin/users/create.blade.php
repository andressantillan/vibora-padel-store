@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Nuevo usuario</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            @include('admin.users._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar usuario</button>
            </div>
        </form>
    </div>
</div>
@endsection