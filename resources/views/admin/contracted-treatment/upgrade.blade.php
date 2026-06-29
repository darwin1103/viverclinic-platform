@extends('layouts.admin')

@section('title', 'Agrandar Paquete')

@section('content')
<div class="container-fluid text-white">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-white">Agrandar Paquete de Tratamiento</h1>
            <p class="mb-4 text-white-50">Permite actualizar el plan de <strong>{{ $contractedTreatment->user->name ?? 'N/A' }}</strong> a un paquete superior de la misma categoría.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.contracted-treatment.upgrade.process', $contractedTreatment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Columna Izquierda: Configuración del Upgrade --}}
            <div class="col-lg-8">
                {{-- Card 1: Paquete Actual --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Paquete Actual</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center text-md-start">
                            <div class="col-md-6 mb-3 mb-md-0 border-end border-secondary border-opacity-25">
                                <h5 class="text-white-50 fw-bold mb-1" style="font-size: 1rem;">Nombre del Plan</h5>
                                <p class="fs-5 fw-bold text-primary">{{ $currentPackage['name'] }}</p>
                                <h5 class="text-white-50 fw-bold mb-1 mt-3" style="font-size: 1rem;">Valor de Compra</h5>
                                <p class="fs-5 fw-bold text-success">$ {{ number_format($currentPackage['price_at_purchase'], 2) }} COP</p>
                            </div>
                            <div class="col-md-6 text-white">
                                <h5 class="text-white-50 fw-bold mb-2" style="font-size: 1rem;">Zonas Seleccionadas Actualmente</h5>
                                <div class="mb-2">
                                    <strong class="text-white d-block mb-1">Zonas Grandes/Pequeñas:</strong>
                                    @if(!empty($contractedTreatment->selected_zones['big']))
                                        @foreach($contractedTreatment->selected_zones['big'] as $zone)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $zone }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-white-50 small">Ninguna</span>
                                    @endif
                                </div>
                                <div>
                                    <strong class="text-white d-block mb-1">Zonas Mini:</strong>
                                    @if(!empty($contractedTreatment->selected_zones['mini']))
                                        @foreach($contractedTreatment->selected_zones['mini'] as $zone)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $zone }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-white-50 small">Ninguna</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Selector de Nuevo Paquete --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold"><i class="bi bi-gift me-2"></i>1. Seleccionar Nuevo Paquete</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($availablePackages as $pkg)
                                <div class="col-md-6">
                                    <div class="card h-100 border-2 package-option-card cursor-pointer position-relative transition-all" 
                                         onclick="selectPackage({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->price }}, {{ $pkg->big_zones }}, {{ $pkg->mini_zones }})"
                                         id="package_card_{{ $pkg->id }}">
                                        <div class="card-body p-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="new_package_id" 
                                                       id="package_radio_{{ $pkg->id }}" value="{{ $pkg->id }}" 
                                                       {{ old('new_package_id') == $pkg->id ? 'checked' : '' }} required>
                                                <label class="form-check-label fw-bold fs-5 text-white ms-2" for="package_radio_{{ $pkg->id }}">
                                                    {{ $pkg->name }}
                                                </label>
                                            </div>
                                            <div class="mt-3 ps-4 text-white">
                                                <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-25 pb-1 mb-1">
                                                    <span class="text-white-50">Precio Nuevo:</span>
                                                    <strong class="text-success">${{ number_format($pkg->price, 2) }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-25 pb-1 mb-1">
                                                    <span class="text-white-50">Zonas Grandes:</span>
                                                    <strong class="text-white">{{ $pkg->big_zones }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-white-50">Zonas Mini:</span>
                                                    <strong class="text-white">{{ $pkg->mini_zones }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Card 3: Selección de Nuevas Zonas --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold"><i class="bi bi-pin-map me-2"></i>2. Distribución de Zonas del Paquete</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info bg-info bg-opacity-10 border-info text-info py-2" id="zones-info-alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Selecciona un nuevo paquete para ver el límite de zonas grandes y mini disponibles.
                        </div>

                        <div class="row mt-3">
                            {{-- Columna Grandes/Pequeñas --}}
                            <div class="col-md-6 mb-4 mb-md-0 border-end border-secondary border-opacity-25">
                                <h5 class="fw-bold text-white border-bottom border-secondary border-opacity-25 pb-2">
                                    Zonas Grandes y Pequeñas 
                                    <span class="badge bg-primary float-end" id="big-zones-counter">0 / 0</span>
                                </h5>
                                
                                <h6 class="text-white-50 fw-bold mt-3" style="font-size: 0.9rem;">Zonas Grandes</h6>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @foreach ($bigZones as $zone)
                                        <div class="form-check p-2 border rounded m-1 zone-check-box">
                                            <input class="form-check-input ms-1 zone-big-check" type="checkbox" name="selected_zones[big][]" id="big_{{ Str::slug($zone) }}" value="{{ $zone }}" {{ in_array($zone, $contractedTreatment->selected_zones['big'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 pe-2 text-white" for="big_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <h6 class="text-white-50 fw-bold mt-2" style="font-size: 0.9rem;">Zonas Pequeñas</h6>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @foreach ($smallZones as $zone)
                                        <div class="form-check p-2 border rounded m-1 zone-check-box">
                                            <input class="form-check-input ms-1 zone-big-check" type="checkbox" name="selected_zones[big][]" id="small_{{ Str::slug($zone) }}" value="{{ $zone }}" {{ in_array($zone, $contractedTreatment->selected_zones['big'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 pe-2 text-white" for="small_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-floating mt-3 text-white">
                                    <input type="text" class="form-control" id="another-big-zone" name="another_big_zone" placeholder="Otra zona grande">
                                    <label for="another-big-zone" class="text-white-50">Agregar otra zona grande o pequeña</label>
                                </div>
                            </div>

                            {{-- Columna Mini Zonas --}}
                            <div class="col-md-6">
                                <h5 class="fw-bold text-white border-bottom border-secondary border-opacity-25 pb-2">
                                    Mini Zonas 
                                    <span class="badge bg-primary float-end" id="mini-zones-counter">0 / 0</span>
                                </h5>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @foreach ($miniZones as $zone)
                                        <div class="form-check p-2 border rounded m-1 zone-check-box">
                                            <input class="form-check-input ms-1 zone-mini-check" type="checkbox" name="selected_zones[mini][]" id="mini_{{ Str::slug($zone) }}" value="{{ $zone }}" {{ in_array($zone, $contractedTreatment->selected_zones['mini'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 pe-2 text-white" for="mini_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-floating mt-3 text-white">
                                    <input type="text" class="form-control" id="another-mini-zone" name="another_mini_zone" placeholder="Otra mini zona">
                                    <label for="another-mini-zone" class="text-white-50">Agregar otra mini zona</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Pago, Comisión y Confirmación --}}
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 2rem;">
                    {{-- Card Pago y Comisión --}}
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 fw-bold"><i class="bi bi-wallet2 me-2"></i>Resumen de Transacción</h6>
                        </div>
                        <div class="card-body">
                            {{-- Resumen Financiero --}}
                            <div class="border-bottom border-secondary border-opacity-25 pb-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white-50">Precio Nuevo Paquete:</span>
                                    <span class="fw-bold text-white" id="txt-new-price">$ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white-50">Precio Anterior:</span>
                                    <span class="text-white-50 fw-bold">$ {{ number_format($currentPackage['price_at_purchase'], 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fs-5 border-top border-secondary border-opacity-25 pt-2">
                                    <strong class="text-success">Diferencia a Pagar:</strong>
                                    <strong class="text-success" id="txt-diff-price">$ 0.00</strong>
                                </div>
                            </div>



                            {{-- Formulario de Pago --}}
                            <h6 class="fw-bold text-white mb-3">3. Registrar Método de Pago</h6>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="CASH" checked onchange="toggleReceiptUpload(false)">
                                    <label class="form-check-label fw-bold text-white" for="payment_cash">
                                        <i class="bi bi-cash-stack me-2 text-success"></i>Efectivo (Aprobación Inmediata)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_transfer" value="TRANSFER" onchange="toggleReceiptUpload(true)">
                                    <label class="form-check-label fw-bold text-white" for="payment_transfer">
                                        <i class="bi bi-bank me-2 text-primary"></i>Transferencia (Pendiente Verificación)
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4 d-none animate__animated animate__fadeIn" id="receipt-upload-container">
                                <label for="payment_receipt" class="form-label fw-bold text-white">Comprobante de Pago</label>
                                <input type="file" name="payment_receipt" id="payment_receipt" class="form-control" accept="image/*">
                                <small class="text-white-50 d-block mt-1">Sube una imagen del comprobante de transferencia bancaria.</small>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success py-2 fw-bold text-uppercase" id="btn-submit-upgrade" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Confirmar Agrandamiento
                                </button>
                                <a href="{{ route('admin.contracted-treatment.show', $contractedTreatment->id) }}" class="btn btn-outline-light py-2">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .package-option-card {
        transition: all 0.25s ease-in-out;
        border-color: rgba(255,255,255,.06) !important;
        background: rgba(8,30,34,.75) !important;
    }
    .package-option-card:hover {
        border-color: var(--vc-primary) !important;
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.4);
    }
    .package-option-card.selected {
        border-color: var(--vc-primary) !important;
        background-color: rgba(19, 160, 178, 0.15) !important;
        box-shadow: 0 0.5rem 1rem rgba(19, 160, 178, 0.3);
    }
    .package-option-card.selected::after {
        content: "\F272";
        font-family: "bootstrap-icons";
        position: absolute;
        top: 10px;
        right: 15px;
        color: var(--vc-primary);
        font-size: 1.25rem;
        font-weight: bold;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .zone-check-box {
        transition: all 0.15s ease-in-out;
        background: rgba(8,30,34,.75) !important;
        border-color: rgba(255,255,255,.08) !important;
    }
    .zone-check-box:hover {
        background-color: rgba(19, 160, 178, 0.15) !important;
        border-color: var(--vc-primary) !important;
    }

</style>
@endpush

@push('scripts')
<script>
    let limitBig = 0;
    let limitMini = 0;
    const oldPackagePrice = {{ $currentPackage['price_at_purchase'] }};

    function selectPackage(id, name, price, big, mini) {
        // Deseleccionar tarjetas
        document.querySelectorAll('.package-option-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Seleccionar tarjeta actual
        const selectedCard = document.getElementById('package_card_' + id);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }

        // Seleccionar radio button
        const radio = document.getElementById('package_radio_' + id);
        if (radio) {
            radio.checked = true;
        }

        // Actualizar límites de zonas
        limitBig = big;
        limitMini = mini;

        // Actualizar texto informativo de zonas
        document.getElementById('zones-info-alert').className = 'alert alert-primary bg-primary bg-opacity-10 border-primary text-primary py-2';
        document.getElementById('zones-info-alert').innerHTML = `<i class="bi bi-info-circle-fill me-2"></i> Límite del Paquete Seleccionado: <strong>${limitBig} Zonas Grandes/Pequeñas</strong> y <strong>${limitMini} Zonas Mini</strong>.`;

        // Actualizar contadores
        updateCounters();

        // Calcular precios
        const diff = price - oldPackagePrice;
        document.getElementById('txt-new-price').textContent = '$ ' + price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('txt-diff-price').textContent = '$ ' + diff.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });



        // Habilitar botón de enviar
        document.getElementById('btn-submit-upgrade').disabled = false;
    }

    function toggleReceiptUpload(show) {
        const container = document.getElementById('receipt-upload-container');
        const receiptInput = document.getElementById('payment_receipt');
        if (show) {
            container.classList.remove('d-none');
            receiptInput.setAttribute('required', 'required');
        } else {
            container.classList.add('d-none');
            receiptInput.removeAttribute('required');
            receiptInput.value = ''; // limpiar
        }
    }

    function updateCounters() {
        const checkedBig = document.querySelectorAll('.zone-big-check:checked').length;
        const checkedMini = document.querySelectorAll('.zone-mini-check:checked').length;

        const bigBadge = document.getElementById('big-zones-counter');
        const miniBadge = document.getElementById('mini-zones-counter');

        bigBadge.textContent = `${checkedBig} / ${limitBig}`;
        miniBadge.textContent = `${checkedMini} / ${limitMini}`;

        // Alerta visual si se exceden los límites
        if (checkedBig > limitBig) {
            bigBadge.className = 'badge bg-danger float-end';
        } else if (checkedBig === limitBig) {
            bigBadge.className = 'badge bg-success float-end';
        } else {
            bigBadge.className = 'badge bg-primary float-end';
        }

        if (checkedMini > limitMini) {
            miniBadge.className = 'badge bg-danger float-end';
        } else if (checkedMini === limitMini) {
            miniBadge.className = 'badge bg-success float-end';
        } else {
            miniBadge.className = 'badge bg-primary float-end';
        }
    }

    // Escuchar cambios en los checkboxes de zonas
    document.querySelectorAll('.zone-big-check').forEach(chk => {
        chk.addEventListener('change', updateCounters);
    });

    document.querySelectorAll('.zone-mini-check').forEach(chk => {
        chk.addEventListener('change', updateCounters);
    });

    // Validar en el submit que no se excedan las zonas permitidas
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedBig = document.querySelectorAll('.zone-big-check:checked').length;
        const checkedMini = document.querySelectorAll('.zone-mini-check:checked').length;

        let errors = [];

        if (checkedBig > limitBig) {
            errors.push(`Has seleccionado más zonas grandes de las permitidas para este paquete (${checkedBig} de ${limitBig}).`);
        }
        if (checkedMini > limitMini) {
            errors.push(`Has seleccionado más mini zonas de las permitidas para este paquete (${checkedMini} de ${limitMini}).`);
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });

    // Auto-seleccionar si ya se había enviado el formulario antes y falló la validación del controlador (old inputs)
    document.addEventListener("DOMContentLoaded", function() {
        const selectedRadio = document.querySelector('input[name="new_package_id"]:checked');
        if (selectedRadio) {
            // Disparar click en la tarjeta contenedora para activar la inicialización
            selectedRadio.closest('.package-option-card').click();
        }
    });
</script>
@endpush
