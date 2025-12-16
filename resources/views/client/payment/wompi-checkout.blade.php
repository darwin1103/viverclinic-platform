@extends('layouts.app')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-lg border-0" style="max-width: 500px; width: 100%;">
        <div class="card-body text-center p-5">
            <h3 class="mb-4">Confirmar Pago en Línea</h3>

            <div class="py-4 rounded mb-4">
                <p class="text-muted mb-1">Total a Pagar</p>
                <h2 class="fw-bold mb-0">${{ number_format($amount, 0, ',', '.') }}</h2>
                <small class="">{{ $description }}</small>
            </div>

            <div id="wompi-widget-container">
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
                        data-customer-data:email="{{ $wompiData['email'] }}"
                        data-customer-data:full-name="{{ $wompiData['full_name'] }}"
                        >
                    </script>
                </form>
            </div>

            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="text-muted text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Cancelar y volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
