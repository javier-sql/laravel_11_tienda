@extends('layouts.layout')

@section('title', 'Productos')

@section('content')
    <h1>Productos</h1>

    @foreach($products as $product)

        <a href="{{ route('product.show', $product->id) }}">
                <p>FOTO</p>                
        </a>

        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            <p>${{ number_format($product->price, 0) }}</p>
            <a href="{{ route('cart.add', $product->id) }}" class="btn btn-success">Agregar al carrito</a>
        </div>

        <a href="{{ route('cart.view')}}">Ir al Carro</a>
    @endforeach
  

@endsection