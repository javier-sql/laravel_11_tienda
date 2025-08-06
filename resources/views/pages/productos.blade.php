@extends('layouts.layout')

@section('title', 'Productos')

@section('content')
    <h1>Productos</h1>

    @foreach($products as $product)

        <a href="{{ route('product.show', $product->id) }}">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" alt="{{ $product->name }}" width="200">             
        </a>

        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            <p>${{ number_format($product->price, 0) }}</p>
        <button class="add-to-cart-btn" data-id="{{ $product->id }}">Agregar al carrito</button>
        </div>
    @endforeach
  

@endsection