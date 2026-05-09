<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
    <head>
        @include('layouts.partials.head')
        <title>@yield('title', 'Víbora Padel Store')</title>
    </head>

    <body>

        <div class="container-fluid py-5 text-center">
            @yield('content')
        </div>
        
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>