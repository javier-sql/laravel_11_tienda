@extends('layouts.layout')
@section('title', 'IniciarSesion')
@section('content')


<div class="container-login">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1>login</h1>

    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div>
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
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