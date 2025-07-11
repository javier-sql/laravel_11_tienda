@php
    $cart = session('cart', []);
    $totalQuantity = array_sum(array_column($cart, 'quantity'));
@endphp


<nav>
    <ul>
        @auth
        <li>Bienvenido, {{ auth()->user()->name }} ({{ auth()->user()->role->name }})</li>
            <li><a href="{{ url('/inicio') }}">Inicio</a></li>
            <li><a href="{{ url('/productos') }}">Productos</a></li>
            @if (auth()->user()->role->name == 'administrador')
                <li><a href="{{ url('/administrador') }}">Panel de Administración</a></li>
            @endif
            <li>
                <a href="{{ url('/cart') }}">
                    Carrito 
                    @if ($totalQuantity > 0)
                        <span id="cart-total-quantity" style="color: red;">({{ $totalQuantity }})</span>
                    @endif
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </li>
        @else
            <li><a href="{{ url('/') }}">Inicio</a></li>
            <li><a href="{{ url('/productos') }}">Productos</a></li>
            <li><a href="{{ url('/login') }}">Iniciar Sesión</a></li>
            <li><a href="{{ url('/register') }}">Registrarse</a></li>
            <li>
                <a href="{{ url('/cart') }}">
                    Carrito 
                    @if ($totalQuantity > 0)
                        <span id="cart-total-quantity" style="color: red;">({{ $totalQuantity }})</span>
                    @endif
                </a>
            </li>
        @endauth
    </ul>
</nav>
