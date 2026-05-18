<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    </head>
    <style>
        .logo {
            max-width: 400px;
            height: auto;
        }

        body {
            font-family: 'Noto Sans', sans-serif;
        }
    </style>
    <body>

        @yield('content')
        <div class="container py-5 text-center">
            {{-- Badge --}}
            <span class="badge rounded-pill text-uppercase mb-3" 
                style="background-color: #E6F1FB; color: #0C447C; font-size: 0.7rem; letter-spacing: 0.05em;">
                E-Commerce · Laravel
            </span>

            {{-- Logo --}}
            <div class="mb-3">
                <img src="{{ asset('images/logo-web.png') }}" 
                    alt="Víbora Padel Store Logo" 
                    class="img-fluid logo" style="max-height: 300px;">
            </div>

            {{-- Título --}}
            <h1 class="fw-bold mb-3">Víbora Padel Store</h1>
            {{-- Ir a iniciar sesion --}}
            <button class="btn btn-primary px-4 py-2" onclick="window.location.href='{{ route('login') }}'">
                Iniciar sesión
            </button>
            {{-- Descripción --}}
            <p class="text-muted mx-auto mb-4 mt-4" style="max-width: 520px; font-size: 1.05rem; line-height: 1.7;">
                Tu tienda online especializada en pádel. Palas, pelotas, ropa, calzado 
                y accesorios para llevar tu juego al siguiente nivel.
            </p>

            {{-- Categorías --}}
            <div class="row justify-content-center g-3 mb-4">
                <div class="col-auto">
                    <div class="card border text-center px-4 py-3">
                        <div class="fs-4 mb-1">🏓</div>
                        <small class="text-muted">Palas & pelotas</small>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="card border text-center px-4 py-3">
                        <div class="fs-4 mb-1">👟</div>
                        <small class="text-muted">Ropa & calzado</small>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="card border text-center px-4 py-3">
                        <div class="fs-4 mb-1">🎒</div>
                        <small class="text-muted">Accesorios</small>
                    </div>
                </div>
            </div>
            
            {{-- Autor --}}
            <p class="text-muted mt-3" style="font-size: 0.8rem;">
                Desarrollado por <strong>Santillán Andrés</strong>
            </p>
        </div>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>