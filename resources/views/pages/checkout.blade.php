@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Checkout</h2>

    {{-- Formulario principal para recoger dirección y ciudad --}}
    <form id="checkoutForm">
        <h4>Dirección de envío</h4>

        {{-- Ciudad destino --}}
        <div class="mb-3">
            <label for="dest_city">Ciudad destino</label>
            <select name="dest_city" id="dest_city" class="form-control" required>
                <option value="">Selecciona ciudad</option>
                @foreach($destCities as $city)
                    <option value="{{ $city['codigoCiudad'] }}">
                        {{ $city['nombreCiudad'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Calle --}}
        <div class="mb-3">
            <label for="street">Calle</label>
            <input type="text" name="street" id="street" class="form-control" required>
        </div>

        {{-- Número --}}
        <div class="mb-3">
            <label for="number">Número</label>
            <input type="text" name="number" id="number" class="form-control" required>
        </div>

        {{-- Complemento / depto / referencia --}}
        <div class="mb-3">
            <label for="complement">Depto / Casa / Referencia</label>
            <input type="text" name="complement" id="complement" class="form-control">
        </div>

        {{-- Tipo de envío --}}
        <div class="mb-3">
            <label>
                <input type="checkbox" name="porPagar" id="porPagar" value="1"> Pagar envío al recibir (por pagar)
            </label>
        </div>

        <h4>Resumen de tu compra</h4>
        <ul id="cartSummary">
            @php 
                $total = 0;
                $shipping = session('shipping_cost', 0); 
            @endphp

            @foreach(session('cart', []) as $item)
                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                @endphp
                <li>{{ $item['name'] }} (x{{ $item['quantity'] }}) - ${{ $subtotal }}</li>
            @endforeach
        </ul>

        <h5>Costo de envío: <span id="shippingCost">${{ $shipping }}</span></h5>
        
        <h5>Total: <span id="grandTotal">${{ $total + $shipping }}</span></h5>

        {{-- Bloque de pago oculto, se muestra cuando la tarifa está calculada --}}
        <div id="pago" style="display: none; margin-top: 20px;">
            <h2>Formulario de pago</h2>
            <form action="{{ route('checkout.process') }}" method="POST">
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
    </form>
    <button id="testShippingBtn">aaa</button>
</div>

@endsection