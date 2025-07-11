@extends('adm.administrador')
@section('title', 'Editar Producto')
@section('contentADM')
<h1>Editar Producto</h1>

<table>
    <tr>
        <th>Id</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Imagen</th>
        <th>Categoría</th>
        <th>Marca</th>
        <th>Autor</th>
        <th>Fecha creacion</th>
        <th>Fecha actualizacion</th>
        <th>Acciones</th>
    </tr>
    @foreach ($products as $product)
    <tr>
        <td>{{ $product->id }} </td>
        <td>{{ $product->name }}</td>
        <td>{{ $product->description }}</td>
        <td>{{ $product->price }}</td>
        <td>{{ $product->stock }}</td>
        <td> <img src='{{ $product->image }}'> </td>
        <td>{{ $product->category->name }}</td>
        <td>{{ $product->brand->name }}</td>
        <td>{{ $product->user->name }}</td>
        <td>{{ $product->created_at }}</td>
        <td>{{ $product->updated_at }}</td>
        <td>
        <button onclick="openModalWithData(
    '{{ $product->id }}', 
    '{{ $product->name }}', 
    '{{ $product->description }}', 
    '{{ $product->price }}', 
    '{{ $product->stock }}', 
    '{{ $product->image }}', 
    '{{ $product->category_id}}', 
    '{{ $product->brand_id}}')">Editar</button>
    <button onclick="openModalDeleteProduct('{{ $product->id }}')" >Borrar</button>   
    </td>
    <tr>
    @endforeach
</table>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Editar producto</h2>
        <form id="editForm" method="POST">

            @csrf
            <label>Id:</label>
            <input type="text" name="id" id="modal-id" readonly>

            <label>Nombre:</label>
            <input type="text" name="name" id="modal-name" required>

            <label>Descripción:</label>
            <textarea name="description" id="modal-description"></textarea>

            <label>Precio:</label>
            <input type="number" name="price" id="modal-price" required>

            <label>Stock:</label>
            <input type="number" name="stock" id="modal-stock" required>

            <label>Imagen (URL):</label>
            <input type="text" name="image" id="modal-image">

            <label>Categoría:</label>
            <select name="category_id" id="modal-category" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <label>Marca:</label>
            <select name="brand_id" id="modal-brand" required>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>

            <div class="modal-buttons">
                <button type="submit">Guardar</button>
                <button type="button" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteProduct" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Eliminar producto</h2>
        <p>¿Estás seguro de que quieres eliminar este producto?</p>
        <form id="deleteForm" method="POST">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="id" id="delete-id">
            <div class="modal-buttons">
                <button type="submit">Eliminar</button>
                <button type="button" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>


@endsection