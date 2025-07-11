@extends('layouts.layout')

@section('title', 'Productos')

@section('content')
    <h1>Productos</h1>

    @foreach($products as $product)
        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            <p>${{ $product->price }}</p>
            <a href="{{ route('cart.add', $product->id) }}" class="btn btn-success">Agregar al carrito</a>
        </div>
    @endforeach
  

@endsection