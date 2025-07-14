@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Finalizar Compra</h2>

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

        <h4>Resumen de tu compra</h4>
        <ul>
            @php $total = 0; @endphp
            @foreach(session('cart', []) as $item)
                @php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; @endphp
                <li>{{ $item['name'] }} (x{{ $item['quantity'] }}) - ${{ $subtotal }}</li>
            @endforeach
        </ul>

        <h5>Total: ${{ $total }}</h5>

        <button class="btn btn-success" type="submit">Pagar con Flow</button>
    </form>
</div>
@endsection