@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>{{ $product->name }}</h2>

    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
        </div>
        <div class="col-md-6">
            <h4>Precio: ${{ $product->price }}</h4>
            <p>{{ $product->description }}</p>
            <p><strong>Stock disponible:</strong> {{ $product->stock }}</p>

        <button class="add-to-cart-btn" data-id="{{ $product->id }}">Agregar al carrito</button>
        </div>
        
        <a href="{{ route('cart.view')}}">Ir al Carro</a>

    </div>
</div>
@endsection