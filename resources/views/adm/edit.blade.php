@extends('adm.administrador')
@section('title', 'Editar Producto')
@section('contentADM')

<h1 class="page-title">Editar Producto</h1>

<div class="table-container">
    <table class="product-table">
        <thead>
            <tr class="table-header">
                <th class="th-id">Id</th>
                <th class="th-name">Nombre</th>
                <th class="th-description">Descripción</th>
                <th class="th-price">Precio</th>
                <th class="th-stock">Stock</th>
                <th class="th-image">Imagen</th>
                <th class="th-category">Categoría</th>
                <th class="th-brand">Marca</th>
                <th class="th-author">Autor</th>
                <th class="th-created">Fecha creación</th>
                <th class="th-updated">Fecha actualización</th>
                <th class="th-actions">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr class="table-row">
                <td class="td-id">{{ $product->id }}</td>
                <td class="td-name">{{ $product->name }}</td>
                <td class="td-description">{{ $product->description }}</td>
                <td class="td-price">${{ number_format($product->price, 2) }}</td>
                <td class="td-stock">{{ $product->stock }}</td>
                <td class="td-image">
                    <img class="product-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                </td>
                <td class="td-category">{{ $product->category->name }}</td>
                <td class="td-brand">{{ $product->brand->name }}</td>
                <td class="td-author">{{ $product->user->name }}</td>
                <td class="td-created">{{ $product->created_at->format('d/m/Y') }}</td>
                <td class="td-updated">{{ $product->updated_at->format('d/m/Y') }}</td>
                <td class="td-actions">
                    <button class="btn-edit" onclick="openModalWithData(
                        '{{ $product->id }}', 
                        '{{ $product->name }}', 
                        '{{ $product->description }}', 
                        '{{ $product->price }}', 
                        '{{ $product->stock }}', 
                        '{{ $product->image }}', 
                        '{{ $product->category_id}}', 
                        '{{ $product->brand_id}}')">Editar</button>
                    <button class="btn-delete" onclick="openModalDeleteProduct('{{ $product->id }}')">Borrar</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close modal-close" onclick="closeModal()">&times;</span>
        <h2 class="modal-title">Editar producto</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data" action="" class="modal-form">

            @csrf
            <label class="modal-label">Id:</label>
            <input type="text" name="id" id="modal-id" readonly class="modal-input">

            <label class="modal-label">Nombre:</label>
            <input type="text" name="name" id="modal-name" required class="modal-input">

            <label class="modal-label">Descripción:</label>
            <textarea name="description" id="modal-description" class="modal-textarea"></textarea>

            <label class="modal-label">Precio:</label>
            <input type="number" name="price" id="modal-price" required class="modal-number">

            <label class="modal-label">Stock:</label>
            <input type="number" name="stock" id="modal-stock" required class="modal-number">

            <label class="modal-label">Imagen:</label>
            <img id="preview-image" src="" alt="Vista previa" width="150" class="modal-image-preview">
            <input type="file" name="image" id="modal-image-input" accept="image/*" class="modal-image-input">

            <label class="modal-label">Categoría:</label>
            <select name="category_id" id="modal-category" required class="modal-select">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <label class="modal-label">Marca:</label>
            <select name="brand_id" id="modal-brand" required class="modal-select">
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>

            <div class="modal-buttons">
                <button type="submit" class="modal-btn-submit">Guardar</button>
                <button type="button" class="modal-btn-cancel" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteProduct" class="modal-delete">
    <div class="modal-content-delete">
        <span class="close modal-delete-close" onclick="closeModal()">&times;</span>
        <h2 class="modal-delete-title">Eliminar producto</h2>
        <p class="modal-delete-text">¿Estás seguro de que quieres eliminar este producto?</p>
        <form id="deleteForm" method="POST" class="modal-delete-form">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="id" id="delete-id" class="modal-delete-id">
            <div class="modal-delete-buttons">
                <button type="submit" class="modal-btn-delete-confirm">Eliminar</button>
                <button type="button" class="modal-btn-delete-cancel" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>



@endsection