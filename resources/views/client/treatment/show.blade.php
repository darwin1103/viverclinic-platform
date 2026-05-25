@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row">
        <div class="col-12">
            <h1>Comprar paquete</h1>
        </div>
    </div>

    <form method="POST" class="g-4" action="{{ route('client.treatment.store') }}" id="purchaseForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">

            <div class="">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="card w-100">
                        <div class="card-body m-0 m-lg-3">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- SECCIÓN 1: ESCOGER PAQUETES Y ZONAS ADICIONALES -->
                            <div class="col-12">
                                <h2 class="section-title">1. Paquete que deseo adquirir</h2>
                                @foreach ($packages as $paquete)
                                    @include('components.client.index.form.package-item', ['item' => $paquete, 'type' => 'package'])
                                @endforeach

                                <h2 class="section-title mt-4">Añadir una zona adicional</h2>
                                @foreach ($additionalZones as $zone)
                                    @include('components.client.index.form.package-item', ['item' => $zone, 'type' => 'additional'])
                                @endforeach
                            </div>

                            <!-- SECCIÓN 2: SELECCIONAR ZONeS -->
                            <div class="col-12">
                                <h2 class="section-title mt-4 mb-3">2. Selecciona las zonas deseadas a realizarte</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Zonas Grandes</h3>
                                        <div id="zones-grandes-container">
                                            @foreach ($bigZones as $zone)
                                                @include('components.client.index.form.zone-checkbox', ['zone' => $zone, 'type' => 'big'])
                                            @endforeach
                                        </div>
                                        <h3>Zonas Pequeñas</h3>
                                        <div id="zones-pequenas-container">
                                            @foreach ($smallZones as $zone)
                                                @include('components.client.index.form.zone-checkbox', ['zone' => $zone, 'type' => 'big'])
                                            @endforeach
                                        </div>
                                        <div class="form-floating mt-3">
                                            <input type="text" class="form-control" id="another-big-zone" name="another_big_zone" placeholder="Otra zona cual:">
                                            <label for="another-big-zone">Otra zona grande (opcional):</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h3>Mini Zonas</h3>
                                        <div id="mini-zones-container">
                                            @foreach ($miniZones as $zone)
                                                @include('components.client.index.form.zone-checkbox', ['zone' => $zone, 'type' => 'mini'])
                                            @endforeach
                                        </div>
                                         <div class="form-floating mt-3">
                                            <input type="text" class="form-control" id="another-mini-zone" name="another_mini_zone" placeholder="Otra mini zona cual:">
                                            <label for="another-mini-zone">Otra mini zona (opcional):</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECCIÓN 3: BOTÓN INSTRUCTIVO -->
                            <div class="col-12 text-center">
                                 <button type="button" class="btn btn-info mt-3" data-bs-toggle="modal" data-bs-target="#instructivoModal">
                                    Ver Imagen de zonas
                                </button>
                            </div>

                            <!-- SECCIÓN 4: RESUMEN DE COMPRA -->
                            <div class="col-12" id="purchase-sumary-container">
                                <h2 class="section-title">Resumen de tu Compra</h2>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody id="purchase-summary"></tbody>
                                        <tfoot>
                                            <tr class="fw-bold fs-5">
                                                <td>PRECIO TOTAL:</td>
                                                <td id="total" class="text-end">$0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input show-terms-conditions-modal" type="checkbox" value="1" id="termsConditions" name="termsConditions" required>
                                        <label class="form-check-label" for="termsConditions">Acepto términos y condiciones</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="notPregnant" name="notPregnant" value="1">
                                        <label class="form-check-label" for="notPregnant">Declaro no estar embarazada</label>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button type="button" class="btn btn-primary btn-lg" id="btn-open-payment-modal" disabled>
                                        Continuar al Pago
                                    </button>
                                </div>
                            </div>

                            {{-- MODAL DE PAGO (DENTRO DEL FORM) --}}
                            <div class="modal fade" id="paymentSelectionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i>Finalizar Compra</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body pt-4">
                                            <div class="row g-0">
                                                {{-- COLUMNA IZQUIERDA: QUÉ PAGAR --}}
                                                <div class="col-12 col-lg-5 pe-lg-4 border-end-lg mb-4 mb-lg-0">
                                                    <h6 class="text-muted text-uppercase small fw-bold mb-3">1. Modalidad de Pago</h6>

                                                    <div class="d-grid gap-3">
                                                        {{-- Opción Cuota (Controlado por JS) --}}
                                                        <label class="card p-3 payment-option-card cursor-pointer" id="option-installment-container" style="display:none">
                                                            <div class="d-flex align-items-center">
                                                                <input type="radio" name="payment_type" value="installment" id="pt_installment" class="form-check-input me-3">
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-bold text-primary">Pago Inicial (1ª Cuota)</div>
                                                                    <small class="text-muted" id="installment-conditions-text">Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión</small>
                                                                </div>
                                                                <div class="fw-bold fs-5" id="modal-installment-amount">$0</div>
                                                            </div>
                                                        </label>

                                                        {{-- Opción Total --}}
                                                        <label class="card p-3 payment-option-card cursor-pointer border-primary shadow-sm">
                                                            <div class="d-flex align-items-center">
                                                                <input type="radio" name="payment_type" value="full" id="pt_full" class="form-check-input me-3" checked>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-bold text-success">Pagar Totalidad</div>
                                                                    <small class="text-muted">Adquiere todo el tratamiento</small>
                                                                </div>
                                                                <div class="fw-bold fs-5" id="modal-total-amount">$0</div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>

                                                {{-- COLUMNA DERECHA: MÉTODO --}}
                                                <div class="col-12 col-lg-7 ps-lg-4">
                                                    <h6 class="text-muted text-uppercase small fw-bold mb-3">2. Método de Pago</h6>

                                                    <div class="row g-3">
                                                        @if(isset($wompiPublicKey) && $wompiPublicKey)
                                                        <div class="col-12 col-md-4">
                                                            <input type="radio" class="btn-check" name="payment_method" id="pm_wompi" value="WOMPI" checked>
                                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pm_wompi">
                                                                <i class="bi bi-credit-card-2-front fs-2 mb-2"></i>
                                                                <span>En Línea</span>
                                                            </label>
                                                        </div>
                                                        @endif

                                                        <div class="col-12 col-md-4">
                                                            <input type="radio" class="btn-check" name="payment_method" id="pm_transfer" value="TRANSFER">
                                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pm_transfer">
                                                                <i class="bi bi-bank fs-2 mb-2"></i>
                                                                <span>Transferencia</span>
                                                            </label>
                                                        </div>

                                                        <div class="col-12 col-md-4">
                                                            <input type="radio" class="btn-check" name="payment_method" id="pm_cash" value="CASH">
                                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="pm_cash">
                                                                <i class="bi bi-cash-coin fs-2 mb-2"></i>
                                                                <span>Efectivo</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    {{-- DETALLES DINÁMICOS --}}
                                                    <div class="mt-4">
                                                        <div id="info-wompi" class="method-info">
                                                            <div class="alert alert-info border-0"><i class="bi bi-shield-check me-2"></i>Redirigiendo a Wompi Bancolombia.</div>
                                                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold">
                                                                <span id="btn-text">Pagar Ahora</span> <i class="bi bi-chevron-right"></i>
                                                            </button>
                                                        </div>

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
                                                                <label class="form-label small fw-bold">Comprobante</label>
                                                                <input type="file" name="payment_receipt" class="form-control" accept="image/*">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold">
                                                                <span id="btn-text">Pagar Ahora</span> <i class="bi bi-chevron-right"></i>
                                                            </button>
                                                        </div>

                                                        <div id="info-cash" class="method-info d-none">
                                                            <div class="alert alert-secondary"><i class="bi bi-shop me-2"></i>Paga en recepción. Tu orden quedará pendiente.</div>
                                                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold">
                                                                <span id="btn-text">Pagar Ahora</span> <i class="bi bi-chevron-right"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Instructivo -->
<x-client.index.form.body-modal />

<div class="modal fade" id="termsConditionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="termsConditionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="termsConditionsModalLabel">{{ __('Terms and Conditions') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $treatment->terms_conditions !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="acceptTermsConditions">{{ __('I accept') }}</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
    <script>
        const packages = @json($packages);
        const additionalZones = @json($additionalZones);
    </script>

    <script type="text/javascript" src="{{ asset('js/client/treatment/show/form.js') }}?v={{ time() }}"></script>

@endpush
