<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
    <head>
        @include('layouts.partials.head')
        <title>@yield('title', 'Víbora Padel Store')</title>
    </head>
    <style>
        body {
            font-family: 'Noto Sans', sans-serif;
        }
    </style>
    <body>

        <div class="d-flex" style="min-height: 100vh;">
            @auth
                @include('components.sidebar')
            @endauth

            <div class="flex-grow-1 p-4 bg-light text-dark">
                @yield('content')
            </div>

        </div>
        
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>