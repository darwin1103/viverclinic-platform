@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-sm mx-auto border-0" style="max-width: 600px;">
        <div class="card-body p-5">
            @if($order->status == 'Pago completado')
                <div class="mb-4 text-success">
                    <i class="bi bi-check-circle-fill display-1"></i>
                </div>
                <h2 class="fw-bold mb-3">¡Pago Exitoso!</h2>
                <p class="text-muted">Tu pago ha sido procesado correctamente.</p>
            @elseif($order->status == 'Pago por verificar')
                <div class="mb-4 text-warning">
                    <i class="bi bi-hourglass-split display-1"></i>
                </div>
                <h2 class="fw-bold mb-3">Pago en Verificación</h2>
                <p class="text-muted">Hemos registrado tu intención de pago. Validaremos el comprobante/efectivo pronto.</p>
            @else
                <div class="mb-4 text-danger">
                    <i class="bi bi-x-circle-fill display-1"></i>
                </div>
                <h2 class="fw-bold mb-3">Problema con el Pago</h2>
                <p class="text-muted">Estado: {{ $order->status }}</p>
            @endif

            <div class="alert alert-light border mt-4 text-start">
                <div class="d-flex justify-content-between">
                    <span>Referencia:</span>
                    <strong>#{{ $order->id }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Monto:</span>
                    <strong>${{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Método:</span>
                    <strong>{{ $order->payment_method }}</strong>
                </div>
            </div>

            <div class="d-grid gap-2 col-md-8 mx-auto mt-5">
                <a href="{{ route('client.schedule-appointment.index', ['contracted_treatment' => $order->contracted_treatment_id]) }}" class="btn btn-primary btn-lg">
                    Gestionar Citas
                </a>
                <a href="{{ route('client.contracted-treatment.index') }}" class="btn btn-outline-secondary">
                    Volver a mis Tratamientos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
