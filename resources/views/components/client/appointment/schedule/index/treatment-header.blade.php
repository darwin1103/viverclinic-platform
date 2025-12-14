@props([
    'treatment',
    'branch',
    'attendedCount',
    'missedCount',
    'pendingCount',
    'paymentIsUpToDate',
    'nextPaymentAmount',
    'nextPaymentDescription',
    'contractedTreatmentId',
    'canPayInstallment',
    'totalRemainingAmount',
])

@php
    // Detectar si es la última cuota (cuando lo que falta pagar es igual a la cuota actual)
    // Usamos una pequeña tolerancia (0.1) para evitar errores de punto flotante en la comparación
    $isLastInstallment = $canPayInstallment && (abs((float)$nextPaymentAmount - (float)$totalRemainingAmount) < 0.1);
@endphp

<div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
    <div>
        <h3 class="fw-semibold mb-1">Control de Tratamiento</h3>
        {{-- Indicador visual de estado de pago --}}
        @if($paymentIsUpToDate)
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                <i class="bi bi-check-circle-fill me-1"></i> Pagos al día
            </span>
        @else
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">
                <i class="bi bi-exclamation-circle-fill me-1"></i> Pago pendiente
            </span>
        @endif
    </div>

    {{-- Botón de Pago --}}
    @if(!$paymentIsUpToDate)
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="bi bi-credit-card me-2"></i> Pagar
        </button>
    @endif
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
    <div class="hstack gap-2 flex-wrap">
        <span class="badge text-bg-success">
            <span class="legend-dot" style="background:#8be0c2"></span>
            Asistidas: {{ $attendedCount }}
        </span>
        <span class="badge text-bg-danger">
            <span class="legend-dot" style="background:#ff9b9b"></span>
            No asistidas: {{ $missedCount }}
        </span>
        <span class="badge text-bg-secondary">
            <span class="legend-dot" style="background:#ccebef"></span>
            Pendientes: {{ $pendingCount }}
        </span>
    </div>
</div>

<div class="row g-4">
    @if($branch->logo)
    <div class="col-12 col-lg-2">
        <img src="{{ $branch->logo ? Storage::url($branch->logo) : '' }}" alt="Logo" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
    </div>
    @endif
    <div class="col-12 col-lg-6">
        <p class="mb-1">
            <strong>Tratamiento:</strong> {{ $treatment->name }}
        </p>
        <p class="mb-1">
            <strong>Sucursal:</strong> {{ $branch->name }}
        </p>
        <p class="mb-1">
            Dirección:
            <a href="{{ $branch->google_maps_url }}" target="_blank" class="link-primary">
                {{ $branch->address }} <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </p>
        <p class="mb-1">
            Teléfono:
            <a href="tel:{{ $branch->phone }}" target="_blank" class="link-primary">
                {{ $branch->phone }}
            </a>
        </p>
    </div>
</div>


{{-- MODAL DE PAGO --}}
@if(!$paymentIsUpToDate)
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i>Realizar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Seleccione la modalidad de pago para continuar con su tratamiento.</p>

                <div class="d-grid gap-3">
                    {{-- Opción Pago de Cuota --}}
                    @if($canPayInstallment && !$isLastInstallment)
                        <button class="btn btn-outline-primary p-3 text-start d-flex justify-content-between align-items-center btn-pay-action"
                                data-type="installment"
                                data-amount="{{ $nextPaymentAmount }}">
                            <div>
                                <div class="fw-bold">{{ $nextPaymentDescription }}</div>
                                <small class="text-muted">Habilita la siguiente sesión</small>
                            </div>
                            <span class="fs-5 fw-bold">${{ number_format($nextPaymentAmount, 0, ',', '.') }}</span>
                        </button>
                    @endif

                    {{-- Opción Pago Total (Siempre disponible si hay deuda) --}}
                    <button class="btn btn-outline-success p-3 text-start d-flex justify-content-between align-items-center btn-pay-action"
                            data-type="full"
                            data-amount="{{ $totalRemainingAmount }}">
                        <div>
                            <div class="fw-bold">
                                {{ $isLastInstallment ? 'Pagar Cuota Final' : 'Pagar Totalidad Restante' }}
                            </div>
                            <small class="text-muted">Habilita todas las sesiones pendientes</small>
                        </div>
                        <span class="fs-5 fw-bold">${{ number_format($totalRemainingAmount, 0, ',', '.') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btns = document.querySelectorAll('.btn-pay-action');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        btns.forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('¿Confirmar pago de simulación?')) return;

                const type = this.getAttribute('data-type');

                fetch("{{ route('client.treatment.pay') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({
                        contracted_treatment_id: {{ $contractedTreatmentId }},
                        payment_type: type
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
@endpush

@endif
