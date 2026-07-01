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

            <div class="flex-grow-1 p-4">
                @include('components.flash-messages')
                @yield('content')
            </div>

        </div>
        
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script>
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.toggle-password');
                if (!btn) return;

                const input = document.getElementById(btn.dataset.target);
                const icon  = btn.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        </script>
    </body>
</html>