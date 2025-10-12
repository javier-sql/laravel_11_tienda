@extends('layouts.layout')

@section('content')
<div class="productdetail-container">
    
        <div class="product-image-detail">
            <img class="img-producto-detail" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
        </div>

        <div class="title-detail">
            <div class="title-brand">{{ $brandName }}</div>

            <div class="title-name">{{ $product->name }}</div>            
        </div>

        <div class="carro-link-detail btn-style-black">
            <a class="btn-carro-detail" href="{{ route('cart.view')}}">Ir al Carro</a>
        </div>


        <!-- Botones de cantidad -->
        <div class="quantity-controls-detail">

            <div class="center-flex">
                <div class="controls-datail-price" id="price-{{ $product->id }}" data-price="{{ $product->price }}">Precio: ${{ number_format($product->price, 0, ',', '.') }}</div>
            </div>
            
            <div class="controls-detail">
                <button class="cart-page-btn decrease-btn-detail" data-id="{{ $product->id }}" data-url="{{ route('cart.decrease.ajax') }}">−</button>
                <span id="quantity-{{ $product->id }}">{{ session('cart')[$product->id]['quantity'] ?? 0 }}</span>
                <button class="cart-page-btn increase-btn-detail" data-id="{{ $product->id }}" data-url="{{ route('cart.increase.ajax') }}">+</button>
            </div>
            <!-- Agregar al carrito -->
            
            <div class="center-flex">
                <button class="btn-add-detail add-to-cart-btn-detail btn-style-black" data-id="{{ $product->id }}">Agregar al carrito</button>
            </div>

        </div>


        <div class="description-detail">
            <div><strong>Stock disponible:</strong> {{ $product->stock }}</div>
            <div class="description-detail-title"><strong>Descripción:</strong></div>
            <div class="description-detail-contenet">{{ $product->description }}</div>
        </div>


</div>
@endsection
