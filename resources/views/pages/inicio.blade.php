@extends('layouts.layout')

@section('title', 'Inicio')

@section('content')
    
    <div class="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('build/assets/imagenes/fondoazul.jpg') }}" alt="Imagen 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('build/assets/imagenes/fondoamarillo.jpg') }}" alt="Imagen 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('build/assets/imagenes/fondonaranja.jpg') }}" alt="Imagen 1">
            </div>
        </div>

        <button type="button" class="prev">&#10094;</button>
        <button type="button" class="next">&#10095;</button>

    </div>
    <section class="latest-products">
        <h2 class="latest-product-h2">Agregados Recientamente</h2>
        <div class="products-container-latest">
            @foreach($latestProducts as $product)
                <div class="product-card-latest">
                    <a href="{{ route('product.show', $product->id) }}"> 
                        <img class="img-producto" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    </a>
                    <h3>{{ $product->name }}</h3>
                    <p>${{ number_format($product->price, 0, ',', '.') }}</p>
                    <a href="#" class="btn">Ver producto</a>
                </div>
            @endforeach
        </div>
    </section>



@endsection
