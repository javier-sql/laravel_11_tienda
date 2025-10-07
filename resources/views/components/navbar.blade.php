
@php
    $cart = session('cart', []);
    $totalQuantity = array_sum(array_column($cart, 'quantity'));

    $totalPrice = 0;

    foreach ($cart as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }

@endphp

    <nav class="navbar">
    <div class="navbar-container">
        @auth
            <ul class="navbar-group navbar-left">
                <li class="font-size">Bienvenid@, {{ auth()->user()->name }}</li>
                <li><a class="menu-text" href="{{ url('/inicio') }}"> Inicio </a></li>
                <li><a class="menu-text" href="{{ url('/productos') }}">Productos</a></li>
                <li class="hamburger-list"><a class="menu-text" href="{{ url('/inicio') }}">Contacto</a></li>
                @if (auth()->user()->role->name == 'administrador')
                    <li><a class="menu-text" href="{{ url('/administrador') }}">Panel de Administración</a></li>
                @endif
            </ul>

            <ul class="navbar-group navbar-center">
                <li><a href="{{ url('/inicio') }}"><img class="logo" src="{{ asset('storage/page/logo.png') }}" alt="Logo"></a></li>                 
            </ul>

            <ul class="navbar-group navbar-right">
                <li><p class="cart-total-price font-size">${{ number_format($totalPrice, 0, ',', '.') }}</p></li>
                <li>
                    <a class="menu-text" href="{{ url('/cart') }}">
                        <img class="carro" src="{{ asset('storage/page/carro.png') }}" alt="">
                            <span class="cart-total-quantity" style="color: red;">({{ $totalQuantity }})</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="menu-cerrar menu-text" type="submit">Cerrar Sesión</button>
                    </form>
                </li>
            </ul>

            <div class="hamburger" id="hamburger">
                <button id="openMenu">☰</button> 
            </div>

            <div class="menu-hamburger" id="menu-hamburger">
                <button id="closeMenu"><span class="cerrar">X</span></button>
                <nav class="hamburger-container">
                    <ul>
                        <li class="hamburger-list">Bienvenid@, {{ auth()->user()->name }}</li>
                        <li class="hamburger-list"><a class="menu-text" href="{{ url('/inicio') }}">Inicio</a></li>
                        <li class="hamburger-list"><a class="menu-text" href="{{ url('/productos') }}">Productos</a></li>
                        @if (auth()->user()->role->name == 'administrador')
                            <li><a class="hamburger-list menu-text" href="{{ url('/administrador') }}">Panel de Administración</a></li>
                        @endif
                        <li class="hamburger-list"><a class="menu-text" href="{{ url('/inicio') }}">Contacto</a></li>

                        <li class="hamburger-list"><p class="cart-total-price font-size">${{ number_format($totalPrice, 0, ',', '.') }}</p></li>
                        <li class="hamburger-list">
                            <a class="menu-text" href="{{ url('/cart') }}">
                                <img class="carro" src="{{ asset('storage/page/carro.png') }}" alt="">
                                    <span class="cart-total-quantity" style="color: red;">({{ $totalQuantity }})</span>
                            </a>
                        </li>
                        <li class="hamburger-list">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="menu-text cursor-pointer" type="submit">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>

            @else
            <ul class="navbar-group navbar-left">
                <li><a class="menu-text" href="{{ url('/inicio') }}">Inicio</a></li>
                <li><a class="menu-text" href="{{ url('/productos') }}">Productos</a></li>
                <li><a class="menu-text" href="{{ url('/inicio') }}">Contacto</a></li>
            </ul>

            <ul class="navbar-group navbar-center">
                <li><a href="{{ url('/inicio') }}"><img class="logo" src="{{ asset('storage/page/logo.png') }}" alt="Logo"></a></li>                 
            </ul>

            <ul class="navbar-group navbar-right">
            <li><p class="cart-total-price font-size">${{ number_format($totalPrice, 0, ',', '.') }}</p></li>
                <li>
                    <a class="" href="{{ url('/cart') }}">
                        <img class="carro" src="{{ asset('storage/page/carro.png') }}" alt="">
                            <span class="cart-total-quantity" style="color: red;">({{ $totalQuantity }})</span>
                    </a>
                </li>
                <li><a class="menu-text" href="{{ url('/login') }}">Iniciar Sesión</a></li>
            </ul>

            <div class="hamburger" id="hamburger">
                <button id="openMenu">☰</button> 
            </div>

            <div class="menu-hamburger" id="menu-hamburger">
                <button id="closeMenu"><span class="cerrar">X</span></button>
                <nav class="hamburger-container">
                    <ul>
                        <li class="hamburger-list"><a class="menu-text" href="{{ url('/inicio') }}">Inicio</a></li>
                        <li class="hamburger-list"><a class="menu-text" href="{{ url('/productos') }}">Productos</a></li>
                        <li class="hamburger-list"><a class="menu-text" href="{{ url('/inicio') }}">Contacto</a></li>

                        <li class="hamburger-list"><p class="cart-total-price">${{ number_format($totalPrice, 0, ',', '.') }}</p></li>
                        <li class="hamburger-list">
                            <a class="menu-text" href="{{ url('/cart') }}">
                                <img class="carro" src="{{ asset('storage/page/carro.png') }}" alt="">
                                    <span class="cart-total-quantity" style="color: red;">({{ $totalQuantity }})</span>
                            </a>
                        </li>
                        <li><a class="menu-text" href="{{ url('/login') }}">Iniciar Sesión</a></li>
                    </ul>
                </nav>
            </div>
            
        @endauth
    </div>
</nav>
