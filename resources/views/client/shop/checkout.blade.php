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
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> Pago</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Por el momento, solo se crea el pedido para registrarlo en el sistema, cuando se implemente wompi aca se mostrar el formulario de pago.
                    </p>

                    <form action="{{ route('client.shop.placeOrder') }}" method="POST">
                        @csrf
                        {{-- Preparamos data limpia para el backend --}}
                        @php
                            $orderItemsData = array_map(function($item) {
                                return [
                                    'product_id' => $item['product']->id,
                                    'quantity' => $item['quantity']
                                ];
                            }, $selectedItems);
                        @endphp
                        <input type="hidden" name="order_items" value="{{ json_encode($orderItemsData) }}">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Confirmar y Comprar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
