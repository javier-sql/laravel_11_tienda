@extends('layouts.layout')

@section('content')
<div id="checkout-container" class="checkout-container">


    <div id="error-message" class="error-message" style="display:none;"></div>

    <h2 class="checkout-title">¿Dónde quieres recibir tu compra?</h2>

<form id="shipping-form" class="shipping-form">
    @csrf

    {{-- Comuna --}}
    <div class="form-group">
        <label for="commune">Selecciona tu comuna</label>
        <select name="commune_id" id="commune" class="form-control" required>
            <option value="">Seleccionar</option>
            @foreach(\App\Models\Commune::all() as $commune)
                <option value="{{ $commune->id }}" 
                    data-price="{{ $commune->price }}"
                    {{ old('commune_id', auth()->check() ? auth()->user()->commune_id : '') == $commune->id ? 'selected' : '' }}>
                    {{ $commune->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Calle --}}
    <div class="form-group mt-2">
        <label for="street">Calle</label>
        <input type="text" name="street" id="street" class="form-control" 
            placeholder="Ejem. Av. Pedro de Valdivia" 
            value="{{ old('street', auth()->check() ? auth()->user()->street : '') }}" required>
    </div>

    {{-- Número --}}
    <div class="form-group mt-2">
        <label for="number">Número</label>
        <input type="text" name="number" id="number" class="form-control" 
            placeholder="Ejem. 123" 
            value="{{ old('number', auth()->check() ? auth()->user()->number : '') }}" required>
    </div>

    {{-- Tipo de propiedad --}}
    <div class="form-group mt-2">
        <label for="property-type">Tipo de propiedad</label>
        <select name="property_type" id="property-type" class="form-control" required>
            <option value="">Seleccionar</option>
            @php
                $selectedType = old('property_type', auth()->check() ? auth()->user()->property_type : '');
            @endphp
            <option value="dpto" {{ $selectedType == 'dpto' ? 'selected' : '' }}>Departamento</option>
            <option value="casa" {{ $selectedType == 'casa' ? 'selected' : '' }}>Casa</option>
            <option value="oficina" {{ $selectedType == 'oficina' ? 'selected' : '' }}>Oficina</option>
            <option value="condominio" {{ $selectedType == 'condominio' ? 'selected' : '' }}>Condominio</option>
        </select>
    </div>



    {{-- Número según tipo --}}
    <div class="form-group mt-2" id="property-number-group" 
        style="display: {{ old('unit', auth()->check() ? auth()->user()->unit : '') ? 'block' : 'none' }};">
        <label for="property-number" id="property-number-label">
            Número {{ old('property_type', auth()->check() ? auth()->user()->property_type : '') }}
        </label>
        <input type="text" name="unit" id="property-number" class="form-control"
            value="{{ old('unit', auth()->check() ? auth()->user()->unit : '') }}">
    </div>


    {{-- Teléfono --}}
    <div class="form-group mt-2">
        <label for="phone">Número de Teléfono de Contacto</label>
        <input type="text" name="phone" id="phone" class="form-control" 
            placeholder="Ejem. 912345678" 
            value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" required>
    </div>

    {{-- Valor del envío --}}
    <div class="form-group mt-3">
        <label>Valor del envío:</label>
        <p id="shipping-price">$0</p>
    </div>

    <div class="form-buttons">
        <button type="button" id="confirm-address" class="">Confirmar dirección</button>
        <button><a class="btn-return-cart btn-volver" href="{{ url('/cart') }}">Volver a Carrito</a></button>
    </div>
</form>

     
</div>



            {{-- Bloque de pago oculto, se muestra cuando la tarifa está calculada --}}
<div id="formulario-pago" class="form-pago-container">

    <h2 class="form-pago-title">Formulario de pago</h2>
    <div class="from-pago">
        <table class="table-form-pago">
            <thead>
                <tr>
                    <th></th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach(session('cart', []) as $item)
                    @php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; @endphp
                    <tr>
                        <td><img src="{{ asset('storage/' .  $item['imagen']) }}" alt="{{ $item['name'] }}" width="50"></td>
                        <td>{{ $item['name'] }}</td>
                        <td>${{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>${{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totales-pago"> 
            <label class="totales">Subtotal productos: $<span id="subtotal-products" data-value="{{ $total }}">{{ number_format($total,0,',','.') }}</span></label>
            <label class="totales">Direccion Confirmada: <span id="address-confirm"></span></label>
            <label class="totales">Costo envío: $<span id="shipping-total">0</span></label>
            <label class="totales"><strong>Total a pagar: $<span id="total">{{ number_format($total,0,',','.') }}</span></strong></label>
        </div>

            <form  class="form-pago" id="pago" action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" required class="form-control"
                                value="{{ auth()->check() ? auth()->user()->name : '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="email">Correo electrónico</label>
                            <input type="email" name="email" required class="form-control"
                                value="{{ auth()->check() ? auth()->user()->email : '' }}">
                        </div>

                        <button type="submit" class="btn-pago" id="btn-pago">
                            <span class="btn-text">Pagar</span>
                        </button>

                        <div id="form-error" class="error-message" style="display:none;"></div>
            </form>

            <div class="form-pago-button">
                <div id="return" class="btn-return-address">
                    <div class="btn-volver">Volver a editar dirección</div>
                </div>
            </div>
    </div>




    
</div>


@endsection