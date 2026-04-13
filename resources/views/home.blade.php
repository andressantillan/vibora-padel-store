<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Bootstrap -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    </head>
    <style>
        .logo {
            max-width: 300px;
            height: auto;
        }
    </style>
    <body>

        @yield('content')
        <div class="container py-5 text-center">
            <h2>Vibora Padel Store</h2>
            <img src="{{ asset('images/logo-web.png') }}" alt="Vibora Padel Store Logo" class="img-fluid mb-4 logo">
            <p>Sitio de venta de productos de padel</p>
            <p>Integrantes: Santillán Andrés</p>
        </div>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>