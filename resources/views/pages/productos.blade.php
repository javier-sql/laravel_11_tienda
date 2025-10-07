@extends('layouts.layout')

@section('title', 'Productos')

@section('content')
    <div class="products-container">
       
        <div class="filter-container">
            <form method="GET" action="{{ route('productos') }}" id="filters-form">
                <div class="filters">
                    <div class="filter-text">Filtros</div>

                    <input class="search-filter" type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Buscar producto...">
                    
                    <div class="category-filter">
                        <label class="category-text">Categoría</label>
                        <div class="category-checkboxes">
                            @foreach($categories as $category)
                                <div>
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" 
                                        {{ is_array(request('category_ids')) && in_array($category->id, request('category_ids')) ? 'checked' : '' }}>
                                    <label>{{ $category->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="brand-filter">
                        <label class="brand-text">Marca</label>
                        <div class="brand-checkboxes">
                            @foreach($brands as $brand)
                                <div>
                                    <input type="checkbox" name="brand_ids[]" value="{{ $brand->id }}" 
                                        {{ is_array(request('brand_ids')) && in_array($brand->id, request('brand_ids')) ? 'checked' : '' }}>
                                    <label>{{ $brand->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="price-filter">
                        <label class="price-text">Precio</label>
                        <div class="price-checkboxes">
                            <div>
                                <input type="checkbox" name="price_ranges[]" value="0-5000"
                                    {{ is_array(request('price_ranges')) && in_array('0-5000', request('price_ranges')) ? 'checked' : '' }}>
                                <label>0 - 5,000</label>
                            </div>
                            <div>
                                <input type="checkbox" name="price_ranges[]" value="5000-10000"
                                    {{ is_array(request('price_ranges')) && in_array('5000-10000', request('price_ranges')) ? 'checked' : '' }}>
                                <label>5,000 - 10,000</label>
                            </div>
                            <div>
                                <input type="checkbox" name="price_ranges[]" value="10000-20000"
                                    {{ is_array(request('price_ranges')) && in_array('10000-20000', request('price_ranges')) ? 'checked' : '' }}>
                                <label>10,000 - 20,000</label>
                            </div>
                            <div>
                                <input type="checkbox" name="price_ranges[]" value="20000-40000"
                                    {{ is_array(request('price_ranges')) && in_array('20000-40000', request('price_ranges')) ? 'checked' : '' }}>
                                <label>20,000 - 40,000</label>
                            </div>
                            <div>
                                <input type="checkbox" name="price_ranges[]" value="60000+"
                                    {{ is_array(request('price_ranges')) && in_array('60000+', request('price_ranges')) ? 'checked' : '' }}>
                                <label>60,000+</label>
                            </div>
                        </div>
                    </div>

                    <div class="restablecer-filter">
                    <a href="{{ route('productos') }}">Restablecer</a>
                    </div>

                </div>
            </form>
        </div>


        <div class="products-section">
            <div class="products-list" id="products-list">
                @include('pages.productslist', ['products' => $products])
            </div>
        </div>

    </div>
@endsection
