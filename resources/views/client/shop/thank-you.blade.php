@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-5">
            @if($order->status == 'Pago completado' || $order->status == 'Pago por verificar')
                <div class="mb-4 text-success">
                    <i class="bi bi-check-circle-fill display-1"></i>
                </div>
                <h2 class="card-title mb-3">¡Gracias por tu compra!</h2>
                <p class="text-muted">Tu orden #{{ $order->id }} ha sido registrada.</p>
                <div class="alert alert-light border">
                    Estado del pago: <strong>{{ $order->payment_status }}</strong>
                </div>
            @else
                <div class="mb-4 text-danger">
                    <i class="bi bi-x-circle-fill display-1"></i>
                </div>
                <h2 class="card-title mb-3">Hubo un problema con tu pago</h2>
                <p class="text-muted">El estado de la transacción es: {{ $order->payment_status }}</p>
            @endif

            <div class="d-grid gap-2 col-8 mx-auto mt-4">
                <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-primary">
                    Ver Detalles de la Orden
                </a>
                <a href="{{ route('client.shop.index') }}" class="btn btn-outline-secondary">
                    Volver a la Tienda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
