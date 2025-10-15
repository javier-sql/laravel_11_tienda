@extends('layouts.layout')

@section('title', 'Registro')

@section('content')
    <div class="container-register">
    <h1>Registro</h1>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- <form method="POST" action="{{ url('/register') }}">
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
    </form> -->

<form method="POST" action="{{ url('/register') }}">
        @csrf
        {{-- Nombre --}}
        <div>
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        {{-- Correo --}}
        <div>
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>

        {{-- Confirmar Contraseña --}}
        <div>
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>

        {{-- Comuna --}}
        <div>
            <label for="commune">Selecciona tu comuna</label>
            <select name="commune_id" id="commune" required>
                <option value="{{ old('commune') }}">Seleccionar</option>
                @foreach(\App\Models\Commune::all() as $commune)
                    <option value="{{ $commune->id }}">
                        {{ $commune->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Calle --}}
        <div>
            <label for="street">Calle</label>
            <input type="text" name="street" id="street" placeholder="Ej: Av. Pedro de Valdivia" value="{{ old('street') }}"required>
        </div>

        {{-- Número --}}
        <div>
            <label for="number">Número</label>
            <input type="text" name="number" id="number" placeholder="Ej: 123" value="{{ old('number') }}" required>
        </div>

        {{-- Tipo de propiedad --}}
        <div>
            <label for="property_type">Tipo de propiedad</label>
            <select name="property_type" id="property_type" required>
                <option value="{{ old('property_type') }}">Seleccionar</option>
                <option value="dpto">Departamento</option>
                <option value="casa">Casa</option>
                <option value="oficina">Oficina</option>
                <option value="condominio">Condominio</option>
            </select>
        </div>

        {{-- Número según tipo --}}
        <div>
            <label for="property_number">Número Dpto/Oficina/Condominio</label>
            <input type="text" name="property_number" id="property_number" placeholder="Ej: 204 o B-12">
        </div>

        {{-- Teléfono --}}
        <div>
            <label for="phone">Número de Teléfono de Contacto</label>
            <input type="text" name="phone" id="phone" placeholder="Ej: 912345678" value="{{ old('phone') }}" required>
        </div>

        <button type="submit">Registrarse</button>
</form>

    <p>¿Ya tienes una cuenta? <a href="{{ url('/login') }}">Inicia sesión aquí</a></p>
</div>

@endsection
