@extends('layouts.layout')

@section('content')
    <div class="container">
        <h2>Carrito de Compras</h2>

        @if(session('cart') && count(session('cart')) > 0)
@php $total = 0; @endphp

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach(session('cart') as $id => $item)
            @php
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            @endphp
            <tr id="product-row-{{ $id }}">
                <td>{{ $item['name'] }}</td>
                <td>${{ number_format($item['price'], 0, ',', '.') }}</td>
                <td>
                    <button 
                        class="btn btn-sm btn-secondary decrease-btn" 
                        data-id="{{ $id }}" 
                        data-url="{{ route('cart.decrease.ajax') }}"
                    >-</button>

                    <span id="quantity-{{ $id }}">{{ $item['quantity'] }}</span>

                    <button 
                        class="btn btn-sm btn-primary increase-btn" 
                        data-id="{{ $id }}" 
                        data-url="{{ route('cart.increase.ajax') }}"
                    >+</button>
                </td>
                <td id="subtotal-{{ $id }}">${{ $subtotal }}</td>
                <td>
                    <a href="{{ route('cart.remove', $id) }}" class="btn btn-danger">Eliminar</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4 id="total-cart">Total: ${{ $total }}</h4>

<a href="{{ route('checkout.view')}}"> <h3>Continuar Compra</h3></a>

<a href="{{ route('cart.clear') }}" class="btn btn-warning">Vaciar Carrito</a>
        @else
            <p>Tu carrito está vacío.</p>
        @endif
    </div>
@endsection
