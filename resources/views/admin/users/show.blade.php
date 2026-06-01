@extends('layouts.app')

@section('title', 'Detalle de usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $user->name }}</h1>
    @can('users.manage')
        <x-show-actions
            :edit-route="route('admin.users.edit', $user)"
            :back-route="route('admin.users.index')" />
    @else
        {{-- Solo el botón volver si no puede editar --}}
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    @endcan
</div>

<div class="card">
    <div class="card-header fw-semibold">Datos del usuario</div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9">{{ $user->name }}</dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9">{{ $user->email }}</dd>

            <dt class="col-sm-3">Rol</dt>
            <dd class="col-sm-9">
                @foreach($user->roles as $role)
                    @php
                        $roleColors = ['admin' => 'danger', 'vendedor' => 'primary', 'deposito' => 'success'];
                    @endphp
                    <span class="badge bg-{{ $roleColors[$role->name] ?? 'secondary' }}">
                        {{ ucfirst($role->name) }}
                    </span>
                @endforeach
            </dd>

            <dt class="col-sm-3">Registrado</dt>
            <dd class="col-sm-9">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection