@extends('adm.administrador')
@section('title', 'Crear Producto')
@section('contentADM')
<div class="container">
    <h2>Agregar Nuevo Producto</h2>

    <!-- Mostrar mensajes de error de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('administrador.Save') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('nombre') }}" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea class="form-control" id="description" name="description" required>{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">price:</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock') }}" required>
        </div>
        <div class="form-group">
            <label for="imagen">Imagen del Producto:</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
        <!--btn-modal category-->
        <button type="button" onclick="categoryModal()">
            Agregar Categoría
        </button>
        <!--btn modal category-edit-->
        <button type="button" onclick="categoryEditModal()">
            Editar Categoría
        </button>

        <div class="form-group">
            <label for="category_id">Categoría:</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <option value="">Seleccione una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="button" onclick="marcaModal()">
            Agregar Marca
        </button>
        <button type="button" onclick="openModalBrandEdit()">
            Editar Marca
        </button>
        <!--modal brand-->
        <div class="form-group">
            <label for="brand_id">Marca:</label>
            <select class="form-control" id="brand_id" name="brand_id" required>
                <option value="">Seleccione una marca</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Producto</button>
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
