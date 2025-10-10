@if ($products->total() >= 12)
<div class="pagination">
    <nav role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-info">
            Mostrando
            <span>{{ $products->firstItem() }}</span>
            a
            <span>{{ $products->lastItem() }}</span>
            de
            <span>{{ $products->total() }}</span>
            resultados
        </div>

        <div class="pagination-links">
            @if ($products->onFirstPage())
                <span class="disabled" aria-disabled="true" aria-label="« Previous">
                    &laquo;
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" rel="prev" aria-label="Previous &laquo;">
                    &laquo;
                </a>
            @endif

            @foreach ($products->links()->elements[0] as $page => $url)
                @if ($page == $products->currentPage())
                    <span class="active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" rel="next" aria-label="Next &raquo;">
                    &raquo;
                </a>
            @else
                <span class="disabled" aria-disabled="true" aria-label="Next »">
                    &raquo;
                </span>
            @endif
        </div>
    </nav>
</div>
@endif


@foreach($products as $product)
    <div class="product-item">
        <a href="{{ route('product.show', $product->id) }}">
            <img class="img-producto" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        </a>
        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            <p>Stock: {{ $product->stock}}</p>
            <div class="product-details">
                <div class="product-price">
                    ${{ number_format($product->price, 0, ',', '.') }}
                </div>
                <button class="btn-carroadd add-to-cart-btn" 
                            data-id="{{ $product->id }}"
                            @if($product->stock == 0) disabled @endif>
                        <img class="img-carroadd cursor-pointer" src="{{ asset('storage/page/carroadd.png') }}" alt="">
                </button>
            </div>
        </div>
    </div>
@endforeach

@if ($products->total() >= 12)
<div class="pagination">
    <nav role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-info">
            Mostrando
            <span>{{ $products->firstItem() }}</span>
            a
            <span>{{ $products->lastItem() }}</span>
            de
            <span>{{ $products->total() }}</span>
            resultados
        </div>

        <div class="pagination-links">
            @if ($products->onFirstPage())
                <span class="disabled" aria-disabled="true" aria-label="« Previous">
                    &laquo;
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" rel="prev" aria-label="Previous &laquo;">
                    &laquo;
                </a>
            @endif

            @foreach ($products->links()->elements[0] as $page => $url)
                @if ($page == $products->currentPage())
                    <span class="active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" rel="next" aria-label="Next &raquo;">
                    &raquo;
                </a>
            @else
                <span class="disabled" aria-disabled="true" aria-label="Next »">
                    &raquo;
                </span>
            @endif
        </div>
    </nav>
</div>
@endif

