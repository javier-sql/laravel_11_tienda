@foreach($products as $product)
    <div class="product-item">
        <a href="{{ route('product.show', $product->id) }}">
            <img class="img-producto" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        </a>
        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            <div class="product-details">
                <div class="product-price">
                    ${{ number_format($product->price, 0, ',', '.') }}
                </div>
                <button class="btn-carroadd add-to-cart-btn" data-id="{{ $product->id }}">
                    <img class="img-carroadd cursor-pointer" src="{{ asset('storage/page/carroadd.png') }}" alt="">
                </button>
            </div>
        </div>
    </div>
@endforeach

<div class="pagination">
    {{ $products->links() }}
</div>
