@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalle de Compra #{{ $order->id }}</h2>
        <a href="{{ route('client.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        {{-- Info General --}}
        <div class="col-12 col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header">Información General</div>
                <div class="card-body">
                    <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y h:i A') }}</p>
                    <p><strong>Estado:</strong> {{ $order->status }}</p>
                    <p><strong>Método de Pago:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
                    <p><strong>Referencia:</strong> {{ $order->payment_reference ?? '---' }}</p>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="col-12 col-md-8 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header">Productos Comprados</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">$ {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end">$ {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                    <td class="text-end fw-bold">$ {{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
