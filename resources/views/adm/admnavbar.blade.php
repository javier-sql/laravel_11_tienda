
<h1>Bienvenido, al Panel de administrador {{ auth()->user()->name }} ({{ auth()->user()->role->name }})</h1>
<nav>
    <ul>
    <li><a href="{{ url('/inicio') }}">Inicio Principal</a></li>
    <li><a href="{{ url('administrador/create') }}">Crear Producto</a></li>
    <li><a href="{{ url('administrador/edit') }}">Editar Producto</a></li>
    </ul>
</nav>