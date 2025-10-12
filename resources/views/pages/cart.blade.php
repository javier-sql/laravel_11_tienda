@extends('layouts.layout')

@section('content')
    @php
        $success = $success ?? null;
        $error = $error ?? null;
    @endphp


    <div id="cart-container" class="container">
        @if($success)
            <div class="div-center">
                <div class="success-message">{{ $success }}</div>
            </div>
        @endif

        @if($error)
            <div class="div-center">
                <div class="error-message-cart" style="display: block;">Pago Rechazado</div>
            </div>
        @endif
        <div class="error-message">Pago Rechazado</div>

        @if(session('cart') && count(session('cart')) > 0)
        @php $total = 0; @endphp
        <div class="cart-container">
            <div class="cart-text">Carrito</div>
            <div class="summary-text">Resumen de la compra</div>
                        <div class="cart-products-container">
                            @foreach(session('cart') as $id => $item)
                                    @php
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    @endphp
                            <div class="card-image">
                                <img class="img-producto-card" src="{{ asset('storage/' . $item['imagen']) }}" alt="{{ $item['name'] }}">
                            </div>
                            <div class="card-name">
                                {{ $item['name'] }}
                            </div>
                            <div class="card-price">
                                ${{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                            <div id="product-row-{{ $id }}" class="card-quantity">
                                <div>                      
                                    <button 
                                        class="cart-page-btn decrease-btn" 
                                        data-id="{{ $id }}" 
                                        data-url="{{ route('cart.decrease.ajax') }}"
                                    >−</button>

                                    <span id="quantity-{{ $id }}" class="cart-quantity-number">{{ $item['quantity'] }}</span>

                                    <button 
                                        class="cart-page-btn increase-btn" 
                                        data-id="{{ $id }}" 
                                        data-url="{{ route('cart.increase.ajax') }}"
                                    >+</button>
                                </div>
                                <div class="max-stock">Máx Stock {{$item['stock']}}</div>
                            </div>
                            <div>
                                <div id="subtotal-{{ $id }}">${{ $subtotal }}</div>
                                <a href="{{ route('cart.remove', $id) }}" class="btn btn-danger">Eliminar</a>                        
                            </div>
                            @endforeach
                        </div>
            <div class="cart-resume-container">
                <div id="total-cart" class="cart-total">Total:  ${{ number_format($total, 0, ',', '.') }}</div>
                <div class="cart-checkout">
                    <a href="{{ route('checkout.view')}}"> <h3>Continuar Compra</h3></a>
                </div>
                <div class="cart-clear">
                    <a href="{{ route('cart.clear') }}" class="btn btn-warning">Vaciar Carrito</a>
                </div>
            </div>
        </div>
        @else
            <div class="empty-cart">
                <p class="empty-title">Tu carrito está vacío.</p>
                <p>Vista nuestros productos. <a class="empty-link" href="{{ url('/productos') }}"> Aquí</a></p>
            </div>
        @endif
    </div>
@endsection
