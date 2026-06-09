@extends('layouts.app') {{-- Asumiendo tu layout de admin --}}

@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Tratamiento</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a href="{{ route('client.contracted-treatment.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left-circle me-1"></i>
                Volver al listado
            </a>
        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-12 p-0">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles del Tratamiento Contratado</h4>
                </div>

                <div class="card-body p-4">

                    {{-- SECCIÓN PRINCIPAL CON LA INFORMACIÓN MÁS IMPORTANTE --}}
                    <div class="p-3 mb-4 rounded bg-primary-subtle">
                        <div class="row align-items-center gy-3">
                            <div class="col-12 col-md-6">
                                <h5 class="text-muted mb-1">Cliente</h5>
                                <p class="h4 fw-light mb-0">{{ $contractedTreatment->user->name ?? 'N/A' }}</p>
                                <small>{{ $contractedTreatment->user->email ?? '' }}</small>
                            </div>
                            <div class="col-12 col-md-3 text-md-center">
                                <h5 class="text-muted mb-1">Estado</h5>
                                @if($contractedTreatment->status == 'Paid')
                                    <span class="badge fs-6 bg-success-subtle border border-success-subtle text-success-emphasis rounded-pill">Pagado</span>
                                @elseif($contractedTreatment->status == 'Pending')
                                    <span class="badge fs-6 bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill">Pendiente</span>
                                @else
                                    <span class="badge fs-6 bg-secondary-subtle border border-secondary-subtle text-secondary-emphasis rounded-pill">{{ $contractedTreatment->status }}</span>
                                @endif
                            </div>
                            <div class="col-12 col-md-3 text-md-end">
                                <h5 class="text-muted mb-1">Total</h5>
                                <p class="h4 fw-bold text-white mb-0">${{ number_format($contractedTreatment->total_price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- DETALLES ADICIONALES --}}
                    <div class="row g-4">
                        {{-- Columna Izquierda: Detalles del Tratamiento --}}
                        <div class="col-12 col-md-6">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-heart-pulse me-2"></i>Información del Tratamiento</h5>
                            <dl class="row">
                                <dt class="col-sm-5">Tratamiento:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->treatment->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-5">Sucursal:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->branch->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-5">Plan de Sesiones:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->sessions }} sesiones</dd>

                                <dt class="col-sm-5">Frecuencia:</dt>
                                <dd class="col-sm-7">Cada {{ $contractedTreatment->days_between_sessions }} días</dd>

                                <dt class="col-sm-5">Fecha de Contratación:</dt>
                                <dd class="col-sm-7">
                                    @php
                                        \Carbon\Carbon::setLocale('es');
                                        echo \Carbon\Carbon::parse($contractedTreatment->created_at)->isoFormat('dddd, D \d\e MMMM, YYYY');
                                    @endphp
                                </dd>

                                <dt class="col-sm-5">Términos y condiciones:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->terms_acepted ? 'Aceptado' : 'No aceptado' }}</dd>

                                <dt class="col-sm-5">Esta en embarazo:</dt>
                                <dd class="col-sm-7">{{ $contractedTreatment->is_pregnant ? 'Si' : 'No' }}</dd>

                            </dl>
                        </div>

                        {{-- Columna Derecha: Desglose del Contrato --}}
                        <div class="col-12 col-md-6">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-receipt-cutoff me-2"></i>Desglose del Contrato</h5>

                            {{-- Paquetes Contratados --}}
                            @php
                                // Aseguramos que los paquetes sean un array.
                                $packagesData = $contractedTreatment->contracted_packages;
                                if (is_string($packagesData)) {
                                    $packagesData = json_decode($packagesData, true);
                                }

                                // Filtramos la colección para quedarnos solo con los paquetes que tienen cantidad > 0.
                                $visiblePackages = collect($packagesData)->where('quantity', '>', 0);
                            @endphp

                            {{-- Solo mostramos el bloque si la colección filtrada no está vacía. --}}
                            @if($visiblePackages->isNotEmpty())
                                <p class="fw-bold mb-1">Paquetes Contratados:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    {{-- Hacemos el bucle sobre la colección ya filtrada. --}}
                                    @foreach($visiblePackages as $package)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $package['quantity'] }}x {{ $package['name'] }}
                                            <span class="badge bg-primary rounded-pill">${{ number_format($package['price_at_purchase'], 2) }} c/u</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Adicionales Contratados --}}
                            @php
                                // Primero, nos aseguramos de que los adicionales sean un array y no un string JSON.
                                $additionalsData = $contractedTreatment->contracted_additionals;
                                if (is_string($additionalsData)) {
                                    $additionalsData = json_decode($additionalsData, true);
                                }

                                // Luego, creamos una colección de Laravel y filtramos para quedarnos solo con los que tienen cantidad > 0.
                                $visibleAdditionals = collect($additionalsData)->where('quantity', '>', 0);
                            @endphp

                            {{-- Ahora la condición principal es muy simple: solo mostramos el bloque si la colección filtrada no está vacía. --}}
                            @if($visibleAdditionals->isNotEmpty())
                                <p class="fw-bold mb-1">Adicionales:</p>
                                <ul class="list-group list-group-flush mb-3">
                                    {{-- Hacemos el bucle sobre la colección ya filtrada. --}}
                                    @foreach($visibleAdditionals as $additional)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $additional['quantity'] }}x {{ $additional['name'] }}
                                            <span class="badge bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill">${{ number_format($additional['price_at_purchase'], 2) }} c/u</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- Zonas Seleccionadas --}}
                    @if($contractedTreatment->selected_zones && (is_array($contractedTreatment->selected_zones)))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-person-standing me-2"></i>Zonas Seleccionadas</h5>

                            {{-- Display Big Zones --}}
                            @if(!empty($contractedTreatment->selected_zones['big']))
                                <h6 class="mt-3 mb-2 fw-bold">Zonas Grandes:</h6>
                                <div>
                                    @foreach($contractedTreatment->selected_zones['big'] as $zoneName)
                                        <span class="badge bg-primary-subtle border border-primary-subtle text-primary-emphasis rounded-pill p-2 me-1 mb-1">
                                            {{ $zoneName }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Display Mini Zones --}}
                            @if(!empty($contractedTreatment->selected_zones['mini']))
                                <h6 class="mt-3 mb-2 fw-bold">Zonas Mini:</h6>
                                <div>
                                    @foreach($contractedTreatment->selected_zones['mini'] as $zoneName)
                                        <span class="badge bg-info-subtle border border-info-subtle text-info-emphasis rounded-pill p-2 me-1 mb-1">
                                            {{ $zoneName }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Fallback for empty zones --}}
                            @if(empty($contractedTreatment->selected_zones['big']) && empty($contractedTreatment->selected_zones['mini']))
                                <p class="text-muted">No se especificaron zonas para este tratamiento.</p>
                            @endif

                        </div>
                    </div>
                    @endif

                    <hr class="my-4">

                    {{-- SECCIÓN DE PAGO (ABONOS / CUOTAS / HISTORIAL DE TRANSACCIONES) --}}
                    <div class="row g-4 mt-2">
                        {{-- Progreso de Pago --}}
                        <div class="col-12 col-lg-6">
                            @if($contractedTreatment->payment_type === 'abono')
                                <h5 class="mb-3 fw-bold text-primary">
                                    <i class="bi bi-wallet2 me-2"></i> Estado de Abonos
                                </h5>
                                <div class="card text-white mb-3" style="border: 1px solid rgba(255, 255, 255, 0.06); background: rgba(255,255,255,0.02)">
                                    <div class="card-body">
                                        @php
                                            $totalPaid = $contractedTreatment->totalPaid();
                                            $totalPrice = $contractedTreatment->total_price;
                                            $remaining = $contractedTreatment->remainingBalance();
                                            $percent = $totalPrice > 0 ? min(100, round(($totalPaid / $totalPrice) * 100)) : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Progreso de Pago: <strong>{{ $percent }}%</strong></span>
                                            <span>${{ number_format($totalPaid, 0, ',', '.') }} / ${{ number_format($totalPrice, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 15px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between text-white-50 small">
                                            <span>Total Pagado: ${{ number_format($totalPaid, 0, ',', '.') }}</span>
                                            <span>Saldo Restante: <strong class="text-info">${{ number_format($remaining, 0, ',', '.') }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            @elseif(!$contractedTreatment->installments->isEmpty())
                                <h5 class="mb-3 fw-bold text-primary">
                                    <i class="bi bi-list-ol me-2"></i> Plan de Cuotas
                                </h5>
                                <div class="table-responsive border rounded">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-transparent">
                                            <tr>
                                                <th class="text-white">#</th>
                                                <th class="text-white">Monto</th>
                                                <th class="text-white">Estado</th>
                                                <th class="text-white">Fecha Pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contractedTreatment->installments->sortBy('installment_number') as $inst)
                                                <tr>
                                                    <td class="fw-bold">{{ $inst->installment_number }}</td>
                                                    <td>${{ number_format($inst->price, 2) }}</td>
                                                    <td>
                                                        @if($inst->status == 'PAID')
                                                            <span class="badge bg-success">Pagada</span>
                                                        @else
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-white-50">
                                                        {{ $inst->paid_at ? $inst->paid_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <h5 class="mb-3 fw-bold text-primary">
                                    <i class="bi bi-wallet2 me-2"></i> Estado de Pago
                                </h5>
                                <div class="alert alert-info border-info">
                                    Este tratamiento no tiene cuotas configuradas (Pago único).
                                    @if($contractedTreatment->status === 'Paid')
                                        <span class="badge bg-success ms-2">Totalmente Pagado</span>
                                    @else
                                        <span class="badge bg-info text-dark ms-2">{{ $contractedTreatment->payment_type === 'abono' ? 'Abono Incompleto' : 'Pago Pendiente' }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Transacciones --}}
                        <div class="col-12 col-lg-6">
                            <h5 class="mb-3 fw-bold text-primary">
                                <i class="bi bi-cash-coin me-2"></i> Historial de Transacciones
                            </h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="bg-transparent">
                                        <tr>
                                            <th class="text-white">Fecha</th>
                                            <th class="text-white">Método</th>
                                            <th class="text-white">Total</th>
                                            <th class="text-white">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($contractedTreatment->orders->sortByDesc('created_at') as $order)
                                            <tr>
                                                <td class="small">
                                                    {{ $order->created_at->format('d/m/Y') }}<br>
                                                    <span class="text-white-50">{{ $order->created_at->format('H:i') }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold d-block text-white">{{ $order->payment_method }}</span>
                                                    <small class="text-white-50 text-truncate d-block" style="max-width: 150px;" title="{{ $order->payment_description }}">
                                                        {{ $order->payment_description }}
                                                    </small>
                                                </td>
                                                <td class="fw-bold text-white">${{ number_format($order->total, 2) }}</td>
                                                <td>
                                                    @if($order->status == 'Pago completado')
                                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Aprobado</span>
                                                    @elseif($order->status == 'Cancelado')
                                                        <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Rechazado</span>
                                                    @elseif($order->status == 'Pago por verificar')
                                                        <span class="badge bg-info text-white"><i class="bi bi-hourglass-split"></i> Por Verificar</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay transacciones registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- BOTONES DE ACCIÓN Y MODAL --}}
                    @if($paymentVerificationPending)
                        <div class="text-center mt-4">
                            <div class="alert alert-info d-inline-block">
                                <i class="bi bi-hourglass-split me-2"></i> Tu pago está en proceso de verificación.
                            </div>
                        </div>
                    @elseif(!$contractedTreatment->isFullyPaid())
                        @if($lastOrderRejected)
                            <div class="alert alert-danger mt-4 text-center">
                                Tu último intento de pago fue rechazado. Por favor intenta nuevamente.<br>
                                <small>{{ $lastOrderMessage }}</small>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success btn-lg pulse-animation" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="bi bi-credit-card me-2"></i> Realizar Pago / Abono
                            </button>
                        </div>

                        {{-- MODAL DE PAGO --}}
                        <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content text-white shadow-lg" style="border: 1px solid rgba(255,255,255,.06); background-color: var(--vc-card, #0f2a30);">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold text-white"><i class="bi bi-wallet2 me-2"></i>Realizar Pago</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('client.treatment.payment.process') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                                        @csrf
                                        <input type="hidden" name="contracted_treatment_id" value="{{ $contractedTreatment->id }}">

                                        <div class="modal-body pt-4 text-start">
                                            <div class="row g-0">

                                                {{-- COLUMNA IZQUIERDA: QUÉ PAGAR --}}
                                                <div class="col-12 col-lg-5 pe-lg-4 border-end mb-4 mb-lg-0" style="border-color: rgba(255,255,255,0.1) !important;">
                                                    <h6 class="text-muted text-uppercase small fw-bold mb-3">1. ¿Qué deseas pagar?</h6>

                                                                                  <div class="d-grid gap-3">
                                                        @php
                                                            $nextInstallment = $contractedTreatment->installments()->where('status', 'PENDING')->orderBy('installment_number')->first();
                                                            $remainingBalance = $contractedTreatment->remainingBalance();
                                                            $hasInstallments = $contractedTreatment->hasInstallments();
                                                        @endphp

                                                        {{-- 1. Opción Cuota (si aplica) --}}
                                                        @if($hasInstallments && $nextInstallment && $remainingBalance > $nextInstallment->price)
                                                            <label class="card p-3 payment-option-card cursor-pointer border-primary shadow-sm" style="background: rgba(255,255,255,0.02)">
                                                                <div class="d-flex align-items-center">
                                                                    <input type="radio" name="payment_type" value="installment" class="form-check-input me-3" checked onchange="updatePaymentUI('installment', {{ $nextInstallment->price }})">
                                                                    <div class="flex-grow-1">
                                                                        <div class="fw-bold text-primary">Cuota #{{ $nextInstallment->installment_number }}</div>
                                                                        <small class="text-white-50">Desbloquea la próxima sesión</small>
                                                                    </div>
                                                                    <div class="fw-bold fs-5">${{ number_format($nextInstallment->price, 0, ',', '.') }}</div>
                                                                </div>
                                                            </label>
                                                        @endif

                                                        {{-- 2. Opción Abono (siempre disponible para abono o si coexiste con cuotas) --}}
                                                        <label class="card p-3 payment-option-card cursor-pointer {{ (!$hasInstallments || !$nextInstallment || $remainingBalance <= $nextInstallment->price) ? 'border-primary shadow-sm' : '' }}" style="background: rgba(255,255,255,0.02)">
                                                            <div class="d-flex flex-column gap-2">
                                                                <div class="d-flex align-items-center">
                                                                    <input type="radio" name="payment_type" value="abono" id="pt_abono_radio" class="form-check-input me-3" {{ (!$hasInstallments || !$nextInstallment || $remainingBalance <= $nextInstallment->price) ? 'checked' : '' }} onchange="updatePaymentUI('abono', document.getElementById('abono_amount').value)">
                                                                    <div class="flex-grow-1">
                                                                        <div class="fw-bold text-info">Realizar Abono</div>
                                                                        <small class="text-white-50">Ingresa un monto personalizado</small>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-2" id="abono-amount-input-container" style="display: {{ (!$hasInstallments || !$nextInstallment || $remainingBalance <= $nextInstallment->price) ? 'block' : 'none' }}">
                                                                    @php
                                                                        $defaultVal = min($minimumAbonoAmount, $remainingBalance);
                                                                    @endphp
                                                                    <input type="text" inputmode="numeric" class="form-control bg-dark text-white border-secondary currency-input" name="abono_amount" id="abono_amount" placeholder="Monto" value="{{ $defaultVal }}" required onchange="updatePaymentUI('abono', this.value)" oninput="updatePaymentUI('abono', this.value)">
                                                                    <span class="text-muted small">Mínimo: ${{ number_format($defaultVal, 0, ',', '.') }} | Máximo: ${{ number_format($remainingBalance, 0, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                        </label>

                                                        {{-- 3. Opción Totalidad Restante --}}
                                                        <label class="card p-3 payment-option-card cursor-pointer {{ (!$nextInstallment || $remainingBalance <= $nextInstallment->price) && !$hasInstallments ? 'border-primary shadow-sm' : '' }}" style="background: rgba(255,255,255,0.02)">
                                                            <div class="d-flex align-items-center">
                                                                <input type="radio" name="payment_type" value="full" class="form-check-input me-3" onchange="updatePaymentUI('full', {{ $remainingBalance }})">
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-bold text-success">Totalidad Restante</div>
                                                                    <small class="text-white-50">Paga el saldo completo</small>
                                                                </div>
                                                                <div class="fw-bold fs-5">${{ number_format($remainingBalance, 0, ',', '.') }}</div>
                                                            </div>
                                                        </label>
                                                    </div>

                                                    <div class="mt-4 p-3 rounded bg-black bg-opacity-25">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Subtotal:</span>
                                                            <span class="fw-bold text-white" id="ui-subtotal">$0</span>
                                                        </div>
                                                        <hr class="border-secondary">
                                                        <div class="d-flex justify-content-between fs-5 fw-bold text-white">
                                                            <span>Total a Pagar:</span>
                                                            <span class="text-white" id="ui-total">$0</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- COLUMNA DERECHA: CÓMO PAGAR --}}
                                                <div class="col-12 col-lg-7 ps-lg-4">
                                                    <h6 class="text-muted text-uppercase small fw-bold mb-3">2. Método de Pago</h6>

                                                    <div class="row g-3">
                                                        @if(\App\Models\Setting::get('wompi_public_key'))
                                                        <div class="col-12 col-md-4">
                                                            <input type="radio" class="btn-check" name="payment_method" id="pm_wompi" value="WOMPI" checked onchange="toggleMethodDetails()">
                                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-white border-secondary" for="pm_wompi">
                                                                <i class="bi bi-credit-card-2-front fs-2 mb-2 text-white"></i>
                                                                <span>En Línea</span>
                                                                <small class="text-muted" style="font-size:0.7rem">PSE, Tarjetas, Nequi</small>
                                                            </label>
                                                        </div>
                                                        @endif

                                                        <div class="col-12 col-md-4">
                                                            <input type="radio" class="btn-check" name="payment_method" id="pm_transfer" value="TRANSFER" onchange="toggleMethodDetails()">
                                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-white border-secondary" for="pm_transfer">
                                                                <i class="bi bi-bank fs-2 mb-2 text-white"></i>
                                                                <span>Transferencia</span>
                                                            </label>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <input type="radio" class="btn-check" name="payment_method" id="pm_cash" value="CASH" onchange="toggleMethodDetails()">
                                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-white border-secondary" for="pm_cash">
                                                                <i class="bi bi-cash-coin fs-2 mb-2 text-white"></i>
                                                                <span>Efectivo</span>
                                                                <small class="text-muted" style="font-size:0.7rem">En Sucursal</small>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    {{-- DETALLES DINÁMICOS --}}
                                                    <div class="mt-4">
                                                        {{-- Info Wompi --}}
                                                        <div id="info-wompi" class="method-info text-white">
                                                            <div class="alert alert-info border-0 d-flex align-items-center text-white" style="background: rgba(255,255,255,0.05)">
                                                                <i class="bi bi-shield-check fs-3 me-3 text-white"></i>
                                                                <div>
                                                                    Serás redirigido a la pasarela de pagos segura de <strong>Wompi Bancolombia</strong>. El pago se verificará automáticamente.
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold text-white">
                                                                Pagar Ahora <i class="bi bi-chevron-right text-white"></i>
                                                            </button>
                                                        </div>

                                                        {{-- Info Transferencia --}}
                                                        <div id="info-transfer" class="method-info d-none text-white">
                                                            <div class="card border-0 mb-3 text-white" style="background: rgba(255,255,255,0.05)">
                                                                <div class="card-body">
                                                                    <h6 class="fw-bold text-white"><i class="bi bi-info-circle me-1 text-white"></i> Datos Bancarios</h6>
                                                                    <ul class="mb-0 small list-unstyled text-white-50">
                                                                        <li><strong>Banco:</strong> Bancolombia</li>
                                                                        <li><strong>Cuenta:</strong> Ahorros</li>
                                                                        <li><strong>Número:</strong> 123-456789</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-white">Subir Comprobante (Obligatorio)</label>
                                                                <input type="file" name="payment_receipt" class="form-control bg-dark text-white border-secondary" accept="image/*">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold text-white">
                                                                Subir y Procesar <i class="bi bi-chevron-right text-white"></i>
                                                            </button>
                                                        </div>

                                                        {{-- Info Efectivo --}}
                                                        <div id="info-cash" class="method-info d-none text-white">
                                                            <div class="alert d-flex align-items-center text-white" style="background: rgba(255,255,255,0.05)">
                                                                <i class="bi bi-shop fs-3 me-3 text-white"></i>
                                                                <div>
                                                                    Debes dirigirte a la recepción de la sucursal <strong>{{ $contractedTreatment->branch->name }}</strong> para realizar el pago. Tu orden quedará pendiente hasta validación.
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-secondary w-100 py-3 fs-5 fw-bold text-white">
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
                            function updatePaymentUI(type, amount) {
                                if (typeof amount === 'string') {
                                    amount = amount.replace(/\D/g, '');
                                }
                                const amountNum = parseInt(amount, 10) || 0;
                                const formatted = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(amountNum);
                                const subtotalEl = document.getElementById('ui-subtotal');
                                const totalEl = document.getElementById('ui-total');
                                if (subtotalEl) subtotalEl.innerText = formatted;
                                if (totalEl) totalEl.innerText = formatted;

                                // Toggle abono input container visibility
                                const abonoContainer = document.getElementById('abono-amount-input-container');
                                if (abonoContainer) {
                                    abonoContainer.style.display = (type === 'abono') ? 'block' : 'none';
                                }

                                document.querySelectorAll('.payment-option-card').forEach(card => {
                                    const radio = card.querySelector('input[type="radio"]');
                                    if(radio && radio.checked) {
                                        card.classList.add('border-primary', 'shadow-sm');
                                    } else {
                                        card.classList.remove('border-primary', 'shadow-sm');
                                    }
                                });
                            }

                            function toggleMethodDetails() {
                                document.querySelectorAll('.method-info').forEach(el => el.classList.add('d-none'));
                                const selectedElement = document.querySelector('input[name="payment_method"]:checked');
                                if (selectedElement) {
                                    const selected = selectedElement.value;
                                    if(selected === 'WOMPI') document.getElementById('info-wompi').classList.remove('d-none');
                                    if(selected === 'TRANSFER') document.getElementById('info-transfer').classList.remove('d-none');
                                    if(selected === 'CASH') document.getElementById('info-cash').classList.remove('d-none');
                                }
                            }

                            document.addEventListener('DOMContentLoaded', () => {
                                const activeRadio = document.querySelector('input[name="payment_type"]:checked');
                                if (activeRadio) {
                                    const type = activeRadio.value;
                                    let amount = 0;
                                    if (type === 'abono') {
                                        const abonoInput = document.getElementById('abono_amount');
                                        amount = abonoInput ? abonoInput.value : 0;
                                    } else if (type === 'installment') {
                                        amount = {{ isset($nextInstallment) ? $nextInstallment->price : 0 }};
                                    } else {
                                        amount = {{ isset($remainingBalance) ? $remainingBalance : 0 }};
                                    }
                                    updatePaymentUI(type, amount);
                                }
                                toggleMethodDetails();
                            });
                        </script>
                        @endpush
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
