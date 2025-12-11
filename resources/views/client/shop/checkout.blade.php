@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Finalizar Compra</h2>

    <div class="row">
        {{-- Lado Izquierdo: Detalle --}}
        <div class="col-12 col-lg-7">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Resumen del Pedido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-white ps-4">Producto</th>
                                    <th class="text-white text-center">Cant.</th>
                                    <th class="text-white text-end">Unitario</th>
                                    <th class="text-white text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedItems as $item)
                                    <tr class="border-bottom">
                                        <td class="ps-4">{{ $item['product']->name }}</td>
                                        <td class="text-center">{{ $item['quantity'] }}</td>
                                        <td class="text-end">$ {{ number_format($item['product']->price, 2) }}</td>
                                        <td class="text-end pe-4 fw-bold">$ {{ number_format($item['subtotal'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-3">TOTAL A PAGAR:</td>
                                    <td class="text-end pe-4 fw-bold pt-3 fs-5">
                                        $ {{ number_format($total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <a href="{{ route('client.shop.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Volver al catálogo
            </a>
        </div>

        {{-- Lado Derecho: Pago --}}
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-wallet2"></i> Selecciona Método de Pago</h5>
                </div>
                <div class="card-body">
                    {{-- Agregamos enctype para la subida de archivos --}}
                    <form action="{{ route('client.shop.placeOrder') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
                        @csrf

                        {{-- Data de items (JSON oculto) --}}
                        @php
                            $orderItemsData = array_map(function($item) {
                                return [
                                    'product_id' => $item['product']->id,
                                    'quantity' => $item['quantity']
                                ];
                            }, $selectedItems);
                        @endphp
                        <input type="hidden" name="order_items" value="{{ json_encode($orderItemsData) }}">

                        {{-- Opciones de Pago --}}
                        <div class="d-flex flex-column gap-2 mb-4">

                            <!-- Opción 1: Pasarela (Simulada) -->
                            @if(isset($wompiData))
                            <div class="form-check card p-3 border-0">
                                <input class="form-check-input" type="radio" name="payment_method" id="method_gateway" value="GATEWAY" checked>
                                <label class="form-check-label w-100 stretched-link fw-bold" for="method_gateway">
                                    <i class="bi bi-credit-card-2-front"></i> Pagar en Línea (Wompi)
                                    <div class="small text-muted fw-normal mt-1">
                                        PSE, Tarjetas de Crédito, Nequi, Bancolombia.
                                    </div>
                                </label>
                            </div>
                            @endif

                            <!-- Opción 2: Efectivo -->
                            <div class="form-check card p-3 border-0">
                                <input class="form-check-input" type="radio" name="payment_method" id="method_cash" value="CASH">
                                <label class="form-check-label w-100 stretched-link fw-bold" for="method_cash">
                                    <i class="bi bi-cash-coin"></i> Efectivo
                                    <div class="small text-muted fw-normal mt-1">
                                        Pagas al retirar el producto en la sucursal.
                                    </div>
                                </label>
                            </div>

                            <!-- Opción 3: Transferencia -->
                            <div class="form-check card p-3 border-0">
                                <input class="form-check-input" type="radio" name="payment_method" id="method_transfer" value="TRANSFER">
                                <label class="form-check-label w-100 fw-bold" for="method_transfer">
                                    <i class="bi bi-bank"></i> Transferencia Bancaria
                                    <div class="small text-muted fw-normal mt-1">
                                        Debes subir el comprobante de pago.
                                    </div>
                                </label>
                            </div>

                            {{-- Sección Oculta para Transferencia --}}
                            <div id="transfer-details" class="card p-3 border-warning mt-1 d-none bg-warning-subtle">
                                <h6 class="small fw-bold">Detalles para transferir:</h6>
                                <p class="small mb-2">
                                    <p class="m-0">Banco: X</p>
                                    <p class="m-0">Cuenta: Ahorros</p>
                                    <p class="m-0">Numero: 123-456789</p>
                                </p>

                                <label for="payment_receipt" class="form-label small fw-bold">Subir Comprobante (Obligatorio)</label>
                                <input class="form-control form-control-sm" type="file" id="payment_receipt" name="payment_receipt" accept="image/*">
                                <div class="form-text small">Formato: JPG, PNG. Máx 2MB.</div>
                            </div>

                        </div>

                        <div class="d-grid gap-2" id="standard-submit-container">
                            <button type="submit" class="btn btn-primary btn-lg" id="btn-confirm">
                                <span id="btn-text">Pagar Ahora</span> <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </form>

                    {{-- BOTÓN WOMPI (Formulario independiente generado por Widget) --}}
                    @if(isset($wompiData))
                    <div id="wompi-widget-container" class="mt-3">
                        <form>
                            <script
                                src="https://checkout.wompi.co/widget.js"
                                data-render="button"
                                data-public-key="{{ $wompiData['public_key'] }}"
                                data-currency="{{ $wompiData['currency'] }}"
                                data-amount-in-cents="{{ $wompiData['amount_in_cents'] }}"
                                data-reference="{{ $wompiData['reference'] }}"
                                data-signature:integrity="{{ $wompiData['signature'] }}"
                                data-redirect-url="{{ $wompiData['redirect_url'] }}"
                                data-customer-data:email="{{ Auth::user()->email }}"
                                data-customer-data:full-name="{{ Auth::user()->name }}"
                                >
                            </script>
                        </form>
                    </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/client/shop/checkout.js') }}"></script>
@endpush
