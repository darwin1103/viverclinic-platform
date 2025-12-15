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
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Modal XL -->
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i>Realizar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('client.treatment.payment.process') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                @csrf
                <input type="hidden" name="contracted_treatment_id" value="{{ $contractedTreatmentId }}">

                <div class="modal-body pt-4">
                    <div class="row g-0">

                        {{-- COLUMNA IZQUIERDA: QUÉ PAGAR --}}
                        <div class="col-12 col-lg-5 pe-lg-4 border-end-lg mb-4 mb-lg-0">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">1. ¿Qué deseas pagar?</h6>

                            <div class="d-grid gap-3">
                                {{-- Opción Cuota (Si aplica) --}}
                                @if($canPayInstallment && !(isset($isLastInstallment) && $isLastInstallment))
                                    <label class="card p-3 payment-option-card cursor-pointer border-primary shadow-sm">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="payment_type" value="installment" class="form-check-input me-3" checked onchange="updatePaymentUI('installment', {{ $nextPaymentAmount }})">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-primary">{{ $nextPaymentDescription }}</div>
                                                <small class="text-muted">Desbloquea solo la próxima sesión</small>
                                            </div>
                                            <div class="fw-bold fs-5">${{ number_format($nextPaymentAmount, 0, ',', '.') }}</div>
                                        </div>
                                    </label>
                                @endif

                                {{-- Opción Total --}}
                                <label class="card p-3 payment-option-card cursor-pointer {{ (!$canPayInstallment || (isset($isLastInstallment) && $isLastInstallment)) ? 'border-primary shadow-sm' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_type" value="full" class="form-check-input me-3"
                                            {{ (!$canPayInstallment || (isset($isLastInstallment) && $isLastInstallment)) ? 'checked' : '' }}
                                            onchange="updatePaymentUI('full', {{ $totalRemainingAmount }})">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-success">{{ isset($isLastInstallment) && $isLastInstallment ? 'Cuota Final' : 'Totalidad Restante' }}</div>
                                            <small class="text-muted">Paga todo y olvídate de cuotas</small>
                                        </div>
                                        <div class="fw-bold fs-5">${{ number_format($totalRemainingAmount, 0, ',', '.') }}</div>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-4 p-3 rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span class="fw-bold" id="ui-subtotal">$0</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fs-5 fw-bold">
                                    <span>Total a Pagar:</span>
                                    <span id="ui-total">$0</span>
                                </div>
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA: CÓMO PAGAR --}}
                        <div class="col-12 col-lg-7 ps-lg-4">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">2. Método de Pago</h6>

                            <div class="row g-3">
                                {{-- Wompi --}}
                                @if(\App\Models\Setting::get('wompi_public_key'))
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="pm_wompi" value="WOMPI" checked onchange="toggleMethodDetails()">
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pm_wompi">
                                        <i class="bi bi-credit-card-2-front fs-2 mb-2"></i>
                                        <span>En Línea</span>
                                        <small class="text-muted" style="font-size:0.7rem">PSE, Tarjetas, Nequi</small>
                                    </label>
                                </div>
                                @endif

                                {{-- Transferencia --}}
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="pm_transfer" value="TRANSFER" onchange="toggleMethodDetails()">
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pm_transfer">
                                        <i class="bi bi-bank fs-2 mb-2"></i>
                                        <span>Transferencia</span>
                                    </label>
                                </div>

                                {{-- Efectivo --}}
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="pm_cash" value="CASH" onchange="toggleMethodDetails()">
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pm_cash">
                                        <i class="bi bi-cash-coin fs-2 mb-2"></i>
                                        <span>Efectivo</span>
                                        <small class="text-muted" style="font-size:0.7rem">En Sucursal</small>
                                    </label>
                                </div>
                            </div>

                            {{-- DETALLES DINÁMICOS --}}
                            <div class="mt-4">
                                {{-- Info Wompi --}}
                                <div id="info-wompi" class="method-info">
                                    <div class="alert alert-info border-0 d-flex align-items-center">
                                        <i class="bi bi-shield-check fs-3 me-3"></i>
                                        <div>
                                            Serás redirigido a la pasarela de pagos segura de <strong>Wompi Bancolombia</strong>.
                                            El pago se verificará automáticamente.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-dark w-100 py-3 fs-5 fw-bold">
                                        Pagar Ahora <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>

                                {{-- Info Transferencia --}}
                                <div id="info-transfer" class="method-info d-none">
                                    <div class="card bg-warning-subtle border-0 mb-3">
                                        <div class="card-body">
                                            <h6 class="fw-bold"><i class="bi bi-info-circle me-1"></i> Datos Bancarios</h6>
                                            <ul class="mb-0 small list-unstyled">
                                                <li><strong>Banco:</strong> X</li>
                                                <li><strong>Cuenta:</strong> Ahorros</li>
                                                <li><strong>Numero:</strong> 123-456789</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Subir Comprobante (Obligatorio)</label>
                                        <input type="file" name="payment_receipt" class="form-control" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold">
                                        <span id="btn-text">Pagar Ahora</span> <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>

                                {{-- Info Efectivo --}}
                                <div id="info-cash" class="method-info d-none">
                                    <div class="alert alert-secondary d-flex align-items-center">
                                        <i class="bi bi-shop fs-3 me-3"></i>
                                        <div>
                                            Debes dirigirte a la recepción de la sucursal <strong>{{ $branch->name }}</strong> para realizar el pago.
                                            Tu orden quedará pendiente hasta validación.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-secondary w-100 py-3 fs-5 fw-bold">
                                        Confirmar Pago en Efectivo
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Variables iniciales PHP
    const amountInstallment = {{ $canPayInstallment ? $nextPaymentAmount : 0 }};
    const amountFull = {{ $totalRemainingAmount }};
    const isLastInstallment = {{ isset($isLastInstallment) && $isLastInstallment ? 'true' : 'false' }};

    function updatePaymentUI(type, amount) {
        // Actualizar visualmente los totales
        const formatted = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(amount);
        document.getElementById('ui-subtotal').innerText = formatted;
        document.getElementById('ui-total').innerText = formatted;

        // Estilos de selección (borde azul)
        document.querySelectorAll('.payment-option-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if(radio.checked) {
                card.classList.add('border-primary', 'shadow-sm');
            } else {
                card.classList.remove('border-primary', 'shadow-sm');
            }
        });
    }

    function toggleMethodDetails() {
        // Ocultar todos
        document.querySelectorAll('.method-info').forEach(el => el.classList.add('d-none'));

        // Mostrar seleccionado
        const selected = document.querySelector('input[name="payment_method"]:checked').value;
        if(selected === 'WOMPI') document.getElementById('info-wompi').classList.remove('d-none');
        if(selected === 'TRANSFER') document.getElementById('info-transfer').classList.remove('d-none');
        if(selected === 'CASH') document.getElementById('info-cash').classList.remove('d-none');
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', () => {
        // Seleccionar opción por defecto
        const defaultType = (isLastInstallment || !{{ $canPayInstallment ? 'true' : 'false' }}) ? 'full' : 'installment';
        const defaultAmount = defaultType === 'installment' ? amountInstallment : amountFull;
        updatePaymentUI(defaultType, defaultAmount);
        toggleMethodDetails();
    });
</script>
<style>
    .payment-option-card { transition: all 0.2s ease; border: 1px solid #dee2e6; }
    .payment-option-card:hover { background-color: #f8f9fa; }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush
@endif
