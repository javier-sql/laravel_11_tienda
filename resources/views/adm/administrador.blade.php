<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script src="{{ asset('build/assets/app.js') }}" defer></script> -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body>
    @include('adm.admnavbar')

    <div class="contenido">
        @yield('contentADM')
    </div>
    
</body>
</html>