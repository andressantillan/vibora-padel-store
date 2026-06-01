@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Editar usuario: {{ $user->name }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.users._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar usuario</button>
            </div>
        </form>
    </div>
</div>
@endsection