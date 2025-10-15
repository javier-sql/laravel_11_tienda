@extends('layouts.layout')
@section('title', 'IniciarSesion')

@section('content')
<div class="container-login">
    <h1>Login</h1>
    @if(session('success'))
        <div class="success-message-login">
           <div>{{ session('success') }}</div> 
        </div>
    @endif



    {{-- 🔹 Mostrar errores de validación o login --}}
    @if ($errors->any())
        <div class="error-message-login">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div>
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <p>¿No tienes una cuenta? <a href="{{ url('/register') }}">Regístrate aquí</a></p>

</div>
@endsection
