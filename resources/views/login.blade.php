@extends('layouts.guest')

@section('title', 'Iniciar Sesión - Víbora Padel Store')

@section('content')
<div>
    <h1 class="mb-4">Iniciar sesión</h1>
    {{-- Mostrar logo --}}
    <div class="mb-4">
        <img src="{{ asset('images/logo-web.png') }}" alt="Víbora Padel Store" style="height: 250px;">
    </div>
    <form method="POST" action="{{ route('login') }}" class="mx-auto" style="max-width: 400px;">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif  
        <div class="mb-3 text-start">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>
        <div class="mb-3 text-start">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
    </form>
</div>
@endsection
