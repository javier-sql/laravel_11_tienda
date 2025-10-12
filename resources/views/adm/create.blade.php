@extends('adm.administrador')
@section('title', 'Crear Producto')
@section('contentADM')

<div class="container container-add-product">
    <h2 class="add-product-title">Agregar Nuevo Producto</h2>

    <!-- Mostrar mensajes de error de validación -->
    @if ($errors->any())
        <div class="alert alert-danger add-product-alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="add-product-alert-item">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('administrador.Save') }}" method="POST" enctype="multipart/form-data" class="add-product-form">
        @csrf
        <div class="form-group add-product-group">
            <label for="nombre" class="add-product-label">Nombre del Producto:</label>
            <input type="text" class="form-control add-product-input" id="name" name="name" value="{{ old('nombre') }}" required>
        </div>

        <div class="form-group add-product-group">
            <label for="description" class="add-product-label">Descripción:</label>
            <textarea class="form-control add-product-textarea" id="description" name="description" required>{{ old('description') }}</textarea>
        </div>

        <div class="form-group add-product-group">
            <label for="price" class="add-product-label">Precio:</label>
            <input type="number" step="0.01" class="form-control add-product-input" id="price" name="price" value="{{ old('price') }}" required>
        </div>

        <div class="form-group add-product-group">
            <label for="stock" class="add-product-label">Stock:</label>
            <input type="number" class="form-control add-product-input" id="stock" name="stock" value="{{ old('stock') }}" required>
        </div>

        <div class="form-group add-product-group">
            <label for="imagen" class="add-product-label">Imagen del Producto:</label>
            <input type="file" class="form-control-file add-product-file" id="image" name="image">
        </div>

        <!-- Botones de categoría -->
        <div class="container-buttons-adm">
            <button type="button" class="btn add-product-btn" onclick="categoryModal()">Agregar Categoría</button>
            <button type="button" class="btn add-product-btn" onclick="categoryEditModal()">Editar Categoría</button>
        </div>

        <div class="form-group add-product-group">
            <label for="category_id" class="add-product-label">Categoría:</label>
            <select class="form-control add-product-select" id="category_id" name="category_id" required>
                <option value="">Seleccione una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Botones de marca -->
         <div class="container-buttons-adm">
            <button type="button" class="btn add-product-btn" onclick="marcaModal()">Agregar Marca</button>
            <button type="button" class="btn add-product-btn" onclick="openModalBrandEdit()">Editar Marca</button>
         </div>

        <div class="form-group add-product-group">
            <label for="brand_id" class="add-product-label">Marca:</label>
            <select class="form-control add-product-select" id="brand_id" name="brand_id" required>
                <option value="">Seleccione una marca</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary add-product-btn-submit">Guardar Producto</button>
    </form>
</div>


<!-- Modal category-add -->
<div id="category-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModalCategory()">&times;</span>
        <h2>Agregar Categoria</h2>
        <form id="categoryForm" method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label for="category_name">Nombre de la categoría:</label>
                <input type="text" class="form-control" id="category_name" name="name" required>
            </div>
            <button type="submit">Guardar Categoría</button>
        </form>
    </div>
</div>

<!-- modal category-edit -->
 <div id="category-edit-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModalCategoryEdit()">&times;</span>
        <h2>Editar Categoria</h2>
        <select class="form-control" id="category_id_edit" onchange="loadCategoryName()">
            <option value="">Seleccione una categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" data-name="{{ $category->name }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <!-- Formulario para editar el nombre -->
        <form id="editCategoryForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_category_name">Nuevo nombre:</label>
                <input type="text" class="form-control" id="edit_category_name" name="name" required>
            </div>
            <button type="submit">Actualizar Categoría</button>
            <button type="button" onclick="modalDeleteCategory()">Eliminar Categoría</button>
        </form>

    </div>
</div>

<!-- modal delete category -->
 <div class="modal" id="deleteCategoryModal">
    <div class="modal-content">
        <span class="close" onclick="closeModalDeleteCategory()">&times;</span>
        <form id="deleteCategoryForm" method="POST">
            @csrf
            @method('DELETE')
            <h2>¿Seguro que quieres eliminar la categoria?</h2>

            <!-- AVISO de error -->
            <p id="categoryWarning" style="color: red; display: none;">
                ⚠️ Debes seleccionar una categoría para eliminar.
            </p>
            <button onclick="DeleteCategory()">
                Eliminar Categoria
            </button>
        </form>   
    </div>
 </div>

<!-- modal brand-add -->
<div id="marca-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModalMarca()">&times;</span>
        <h2>Agregar Marca</h2>
        <form id="brandForm" method="POST" action="{{ route('brand.store') }}">
            @csrf
        <div class="form-group">
            <label for="brand_name">Nombre de la Marca:</label>
            <input type="text" class="form-control" id="brand_name" name="name" required>
        </div>
        <button type="submit">Guardar Marca</button>
        </form>
    </div>
</div>
<!-- modal brand-edit -->
<div id="brand-edit-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModalBrandEdit()">&times;</span>
        <h2>Editar Marca</h2>

        <select class="form-control" id="brand_id_edit" onchange="loadBrandName()">
            <option value="">Seleccione una marca</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" data-name="{{ $brand->name }}">{{ $brand->name }}</option>
            @endforeach
        </select>

        <!-- Formulario para editar el nombre -->
        <form id="editBrandForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_brand_name">Nuevo nombre:</label>
                <input type="text" class="form-control" id="edit_brand_name" name="name" required>
            </div>
            <button type="submit">Actualizar Marca</button>
            <button type="button" onclick="openModalDeleteBrand()">Eliminar Marca</button>
        </form>
    </div>
</div>

<!-- Modal eliminar marca -->
<div class="modal" id="deleteBrandModal">
    <div class="modal-content">
        <span class="close" onclick="closeModalDeleteBrand()">&times;</span>
        <form id="deleteBrandForm" method="POST">
            @csrf
            @method('DELETE')
            <h2>¿Seguro que quieres eliminar la marca?</h2>

            <!-- AVISO de error -->
            <p id="brandWarning" style="color: red; display: none;">
                ⚠️ Debes seleccionar una marca para eliminar.
            </p>

            <button type="submit" onclick="DeleteBrand()">
                Eliminar Marca
            </button>
        </form>
    </div>
</div>



@endsection
