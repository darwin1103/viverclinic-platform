@forelse($products as $product)
    <div class="col">
        <div class="card h-100 shadow-sm product-card border-0"
             data-id="{{ $product->id }}"
             data-name="{{ $product->name }}"
             data-price="{{ $product->price }}"
             data-stock="{{ $product->stock }}">

            <div class="card-body p-3 d-flex flex-column">
                <h6 class="card-title fw-bold text-truncate" title="{{ $product->name }}">
                    {{ $product->name }}
                </h6>
                <div class="d-flex justify-content-between align-items-end mt-auto">
                    <div>
                        <div class="text-primary fw-bold">$ {{ number_format($product->price, 2) }}</div>
                        <small class="text-muted">Stock: {{ $product->stock }}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary btn-add-cart">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <div class="text-muted">
            <i class="bi bi-box-seam display-4"></i>
            <p class="mt-2">No hay productos disponibles con los filtros actuales.</p>
        </div>
    </div>
@endforelse
