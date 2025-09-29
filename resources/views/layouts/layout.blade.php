<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Título por Defecto')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script src="{{ asset('build/assets/app.js') }}" defer></script> -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body>
    @include('components.navbar')
    <main>
        <div class="contenido">
        @yield('content')
        </div> 
    </main>
        
    @include('components.footer')

</body>
</html>
