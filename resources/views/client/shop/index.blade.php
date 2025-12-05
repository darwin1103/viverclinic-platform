@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Catálogo de Productos</h2>
            <p class="text-muted">Selecciona la cantidad de los productos que deseas comprar.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Formulario envuelve todo el grid para enviar selección al checkout --}}
    <form action="{{ route('client.shop.checkout') }}" method="POST" id="shop-form">
        @csrf
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
            @forelse($products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        {{-- Placeholder de imagen ya que dijiste que no hay imágenes --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <h6 class="card-subtitle mb-2 text-primary fw-bold">
                                $ {{ number_format($product->price, 2, ',', '.') }}
                            </h6>
                            <p class="card-text text-muted small">
                                Disponible: {{ $product->stock }} unidades
                            </p>

                            <div class="mt-auto pt-3 border-top">
                                <label for="qty_{{ $product->id }}" class="form-label small">Cantidad:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary btn-minus" type="button" data-target="qty_{{ $product->id }}">-</button>
                                    <input type="number"
                                           class="form-control text-center quantity-input"
                                           id="qty_{{ $product->id }}"
                                           name="quantities[{{ $product->id }}]"
                                           value="0"
                                           min="0"
                                           max="{{ $product->stock }}"
                                           readonly> {{-- Readonly para obligar uso de botones y validar JS mejor --}}
                                    <button class="btn btn-outline-secondary btn-plus" type="button"
                                            data-target="qty_{{ $product->id }}"
                                            data-max="{{ $product->stock }}">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No hay productos disponibles en tu sucursal en este momento.</div>
                </div>
            @endforelse
        </div>

        {{-- Footer flotante o fijo al final para el botón de comprar --}}
        @if($products->count() > 0)
        <div class="fixed-bottom border-top shadow py-3">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="h5 mb-0">Total estimado: <span id="grand-total" class="text-primary fw-bold">$ 0</span></div>
                <button type="submit" class="btn btn-success btn-lg" id="btn-buy" disabled>
                    Ir a Pagar <i class="bi bi-cart-check"></i>
                </button>
            </div>
        </div>
        @endif
    </form>
</div>

{{-- Espacio para que el footer no tape contenido --}}
<div style="height: 100px;"></div>

<script>
    // Pequeño script vanilla inline para la lógica de suma visual
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('.quantity-input');
        const totalDisplay = document.getElementById('grand-total');
        const buyBtn = document.getElementById('btn-buy');
        const products = @json($products->map(fn($p) => ['id' => $p->id, 'price' => $p->price]));

        const updateState = () => {
            let total = 0;
            let count = 0;

            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if(qty > 0) {
                    const pid = parseInt(input.id.split('_')[1]);
                    const product = products.find(p => p.id === pid);
                    if(product) {
                        total += qty * parseFloat(product.price);
                        count += qty;
                    }
                }
            });

            totalDisplay.innerText = '$ ' + new Intl.NumberFormat('es-CO').format(total);
            buyBtn.disabled = count === 0;
        };

        // Eventos botones +/-
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const max = parseInt(btn.dataset.max);
                let val = parseInt(input.value);
                if(val < max) {
                    input.value = val + 1;
                    updateState();
                }
            });
        });

        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                let val = parseInt(input.value);
                if(val > 0) {
                    input.value = val - 1;
                    updateState();
                }
            });
        });
    });
</script>
@endsection
