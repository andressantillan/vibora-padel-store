@extends('layouts.app')

@section('title', 'Usuarios del local')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Usuarios del local</h1>
    @can('users.manage')
        <x-new-button :route="route('admin.users.create')" label="Nuevo usuario" />
    @endcan
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

<x-filter-bar :action="route('admin.users.index')">
    <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Nombre o email">
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Rol</label>
        <select name="role" class="form-select">
            <option value="">Todos</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>
</x-filter-bar>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th style="width:160px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="text-muted">{{ $user->id }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none">
                            {{ $user->name }}
                        </a>
                        @if($user->id === auth()->id())
                            <span class="badge bg-light text-dark border ms-1">Vos</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            @php
                                $roleColors = ['admin' => 'danger', 'vendedor' => 'primary', 'deposito' => 'success'];
                            @endphp
                            <span class="badge bg-{{ $roleColors[$role->name] ?? 'secondary' }}">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </td>
                    <td class="text-end">
                        <x-row-actions
                            :show-route="route('admin.users.show', $user)"
                            :edit-route="route('admin.users.edit', $user)"
                            :delete-route="route('admin.users.destroy', $user)"
                            item-name="el usuario {{ $user->name }}" 
                            :can-edit="auth()->user()->can('users.manage')"
                            :can-delete="auth()->user()->can('users.manage') && $user->id !== auth()->id()" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No hay usuarios cargados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$users" />
@endsection