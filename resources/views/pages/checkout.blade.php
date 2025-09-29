@extends('layouts.layout')

@section('content')
<div class="container">
    
    <h2>Checkout</h2>

    <form id="shipping-form">
        @csrf
        {{-- Comuna --}}
        <div class="form-group">
            <label for="commune">Selecciona tu comuna</label>
            <select name="commune_id" id="commune" class="form-control" required>
                <option value="">Seleccionar</option>
                @foreach(\App\Models\Commune::all() as $commune)
                    <option value="{{ $commune->id }}" data-price="{{ $commune->price }}">
                        {{ $commune->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Calle --}}
        <div class="form-group mt-2">
            <label for="street">Calle</label>
            <input type="text" name="street" id="street" class="form-control" required>
        </div>

        {{-- Número --}}
        <div class="form-group mt-2">
            <label for="number">Número</label>
            <input type="text" name="number" id="number" class="form-control" required>
        </div>

        {{-- Tipo de propiedad --}}
        <div class="form-group mt-2">
            <label for="property-type">Tipo de propiedad</label>
            <select name="property_type" id="property-type" class="form-control" required>
                <option value="">Seleccionar</option>
                <option value="dpto">Departamento</option>
                <option value="casa">Casa</option>
                <option value="oficina">Oficina</option>
                <option value="condominio">Condominio</option>
            </select>
        </div>

        {{-- Número según tipo --}}
        <div class="form-group mt-2" id="property-number-group" style="display:none;">
            <label for="property-number" id="property-number-label"></label>
            <input type="text" name="property_number" id="property-number" class="form-control">
        </div>

        {{-- Número de teléfono --}}
        <div class="form-group mt-2">
            <label for="phone">Número de Teléfono de Contacto</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        {{-- Valor del envío --}}
        <div class="form-group mt-3">
            <label>Valor del envío:</label>
            <p id="shipping-price">$0</p>
        </div>

        <button type="button" id="confirm-address" class="btn btn-primary mt-2">Confirmar dirección</button>
    </form>
</div>



        {{-- Bloque de pago oculto, se muestra cuando la tarifa está calculada --}}
    <div id="pago" style="margin-top: 20px;">
        <h2>Formulario de pago</h2>

        <table class="table">
    <thead>
        <tr>
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
                <td>{{ $item['name'] }}</td>
                <td>${{ number_format($item['price'], 0, ',', '.') }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>${{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Subtotal productos: $<span id="subtotal-products" data-value="{{ $total }}">{{ number_format($total,0,',','.') }}</span></p>
<p>Costo envío: $<span id="shipping-total">0</span></p>
<p><strong>Total a pagar: $<span id="total">{{ number_format($total,0,',','.') }}</span></strong></p>




<form  id="pago" style="display:block" action="{{ route('checkout.process') }}" method="POST">
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

            <button type="submit" class="btn btn-success">Pagar con Flow</button>
        </form>
</div>




    
</div>




@endsection