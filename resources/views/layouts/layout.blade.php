<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Título por Defecto')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body>
    @include('components.navbar')

    <div class="contenido">
        @yield('content')
    </div>

    <!-- Aquí puedes incluir tus scripts -->

    @include('components.footer')

</body>
</html>
