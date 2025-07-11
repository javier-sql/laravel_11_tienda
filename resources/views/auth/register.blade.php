@extends('layouts.layout')

@section('title', 'Registro')

@section('content')
    <h1>Formulario de Registro</h1>

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario de registro -->
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <div>
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div>
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>

        <button type="submit">Registrarse</button>
    </form>
@endsection
