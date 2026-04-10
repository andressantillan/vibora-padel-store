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
    <body>

        @yield('content')
        <div class="container py-5">
            <h2>Hola, próximamente esto va a ser Vibora Padel Store</h2>
        </div>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>