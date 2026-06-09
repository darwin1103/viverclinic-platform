@extends('layouts.admin')

@section('title', 'Asignar Tratamiento Antiguo')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Asignar Tratamiento a Usuario Antiguo</h1>
            <p class="mb-4">Asigna un tratamiento previo a un paciente migrado. Esto no generará registros contables ni ventas.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.legacy-treatments.store') }}" method="POST">
                @csrf
                
                <h4 class="mb-3 text-primary border-bottom pb-2">1. Datos Principales</h4>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label fw-bold">Paciente (Solo usuarios antiguos)</label>
                        <select name="user_id" id="user_id" class="form-select select2" required>
                            <option value="">Seleccione un paciente...</option>
                            @foreach($legacyUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="branch_id" class="form-label fw-bold">Sucursal</label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="">Seleccione sucursal...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="treatment_id" class="form-label fw-bold">Tratamiento</label>
                        <select name="treatment_id" id="treatment_id" class="form-select select2" required>
                            <option value="">Seleccione tratamiento...</option>
                            @foreach($treatments as $treatment)
                                <option value="{{ $treatment->id }}">{{ $treatment->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="sessions" class="form-label fw-bold">Sesiones Restantes a Asignar</label>
                        <input type="number" name="sessions" id="sessions" class="form-control" min="1" value="1" required>
                        <small class="text-muted">Número de sesiones que el paciente aún tiene pendientes por tomar.</small>
                    </div>
                </div>

                <h4 class="mb-3 text-primary border-bottom pb-2">2. Zonas del Tratamiento</h4>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-secondary">Zonas Grandes</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($bigZones as $zone)
                                <div class="form-check form-check-inline p-2 m-0">
                                    <input class="form-check-input ms-1" type="checkbox" name="selected_zones[big][]" id="big_{{ Str::slug($zone) }}" value="{{ $zone }}">
                                    <label class="form-check-label ms-2 pe-2" for="big_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                </div>
                            @endforeach
                        </div>
                        
                        <h5 class="text-secondary mt-3">Zonas Pequeñas</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($smallZones as $zone)
                                <div class="form-check form-check-inline p-2 m-0">
                                    <input class="form-check-input ms-1" type="checkbox" name="selected_zones[big][]" id="small_{{ Str::slug($zone) }}" value="{{ $zone }}">
                                    <label class="form-check-label ms-2 pe-2" for="small_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="another-big-zone" name="another_big_zone" placeholder="Otra zona grande">
                            <label for="another-big-zone">Otra zona grande o pequeña (opcional)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-secondary">Mini Zonas</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($miniZones as $zone)
                                <div class="form-check form-check-inline p-2 m-0">
                                    <input class="form-check-input ms-1" type="checkbox" name="selected_zones[mini][]" id="mini_{{ Str::slug($zone) }}" value="{{ $zone }}">
                                    <label class="form-check-label ms-2 pe-2" for="mini_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="another-mini-zone" name="another_mini_zone" placeholder="Otra mini zona">
                            <label for="another-mini-zone">Otra mini zona (opcional)</label>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3 text-primary border-bottom pb-2">3. Configuración de Pago</h4>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label for="payment_type" class="form-label fw-bold">Modalidad de Pago</label>
                        <select name="payment_type" id="payment_type" class="form-select" onchange="togglePaymentFields()" required>
                            <option value="full">Totalmente Pagado</option>
                            <option value="installment">Cuotas</option>
                            <option value="abono">Abono (Saldo pendiente)</option>
                        </select>
                    </div>
                </div>

                {{-- Abono Fields --}}
                <div class="row mb-4 d-none" id="abono_fields_container">
                    <div class="col-md-6 mb-3">
                        <label for="total_price" class="form-label fw-bold">Precio Total del Paquete</label>
                        <input type="text" inputmode="numeric" name="total_price" id="total_price" class="form-control currency-input" value="0">
                        <small class="text-muted">El precio total configurado para el paciente migrado.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="legacy_paid_amount" class="form-label fw-bold">Monto ya Pagado / Abonado</label>
                        <input type="text" inputmode="numeric" name="legacy_paid_amount" id="legacy_paid_amount" class="form-control currency-input" value="0">
                        <small class="text-muted">El monto total que el paciente ya canceló previamente (saldo restante = precio total - monto ya pagado).</small>
                    </div>
                </div>

                {{-- Installment Fields --}}
                <div class="row mb-4 d-none" id="installment_fields_container">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold d-block">Configurar Cuotas Pendientes</label>
                        <small class="text-muted d-block mb-3">Se generará una cuota por cada sesión restante. Todas se crearán en estado PENDIENTE. Si una cuota tiene valor mayor a $0, se le pedirá al paciente pagarla antes de agendar la cita correspondiente.</small>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="text-white" style="width: 25%;">Sesión / Cuota</th>
                                        <th class="text-white">Precio/Monto de la Cuota (COP)</th>
                                    </tr>
                                </thead>
                                <tbody id="installments_table_body">
                                    {{-- Dynamically generated by JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i> 
                    Al guardar, el paciente deberá iniciar sesión en su cuenta y firmar el consentimiento médico específico de este tratamiento antes de poder agendar su cita.
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Asignar Tratamiento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Select2 container override to match dashboard.css inputs (#0d2a30 background, #18464f borders, #d7ffff text) */
    .select2-container--bootstrap-5 .select2-selection {
        background-color: #0d2a30 !important;
        border: 1px solid #18464f !important;
        color: #d7ffff !important;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
        height: calc(2.25rem + 2px) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23d7ffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        background-repeat: no-repeat;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        display: none !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #d7ffff !important;
        padding-left: 0 !important;
        line-height: 1.5 !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        background-color: #0d2a30 !important;
        border: 1px solid #18464f !important;
        color: #d7ffff !important;
    }

    .select2-container--bootstrap-5 .select2-search__field {
        background-color: #0c2024 !important;
        border: 1px solid #18464f !important;
        color: #d7ffff !important;
    }

    .select2-container--bootstrap-5 .select2-results__option {
        background-color: #0d2a30 !important;
        color: #d7ffff !important;
        padding: 6px 12px;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected],
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected="true"] {
        background-color: #18464f !important;
        color: #ffffff !important;
    }

    .select2-container--bootstrap-5 .select2-results__option[aria-selected="true"] {
        background-color: #123c44 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePaymentFields() {
        const type = document.getElementById('payment_type').value;
        const abonoContainer = document.getElementById('abono_fields_container');
        const installmentContainer = document.getElementById('installment_fields_container');

        // Reset required properties
        document.getElementById('total_price').required = false;
        document.getElementById('legacy_paid_amount').required = false;

        abonoContainer.classList.add('d-none');
        installmentContainer.classList.add('d-none');

        if (type === 'abono') {
            abonoContainer.classList.remove('d-none');
            document.getElementById('total_price').required = true;
            document.getElementById('legacy_paid_amount').required = true;
        } else if (type === 'installment') {
            installmentContainer.classList.remove('d-none');
            updateInstallmentsList();
        }
    }

    function updateInstallmentsList() {
        const sessionsInput = document.getElementById('sessions');
        const sessionsCount = parseInt(sessionsInput.value) || 1;
        const tableBody = document.getElementById('installments_table_body');
        
        // Preserve current values if inputs already exist
        const existingValues = {};
        tableBody.querySelectorAll('.installment-price-input').forEach(input => {
            const num = input.dataset.number;
            existingValues[num] = input.value;
        });

        tableBody.innerHTML = '';

        for (let i = 1; i <= sessionsCount; i++) {
            const value = existingValues[i] !== undefined ? existingValues[i] : 0;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="fw-bold text-white">Cuota #${i}</td>
                <td>
                    <input type="text" inputmode="numeric" 
                           name="installments[${i}][price]" 
                           class="form-control installment-price-input text-white currency-input" 
                           style="background-color: #0d2a30; border: 1px solid #18464f;"
                           data-number="${i}" 
                           value="${value}" 
                           required>
                </td>
            `;
            tableBody.appendChild(row);
        }
        
        // Trigger formatting on the newly added inputs
        tableBody.querySelectorAll('.currency-input').forEach(input => {
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.jQuery !== 'undefined') {
            const script = document.createElement('script');
            script.src = "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js";
            script.onload = function() {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            };
            document.head.appendChild(script);
        } else {
            console.error('jQuery is not loaded. Select2 search will not function.');
        }

        // Toggle on load
        togglePaymentFields();

        // Listen to sessions input changes
        const sessionsInput = document.getElementById('sessions');
        if (sessionsInput) {
            sessionsInput.addEventListener('input', function() {
                if (document.getElementById('payment_type').value === 'installment') {
                    updateInstallmentsList();
                }
            });
            sessionsInput.addEventListener('change', function() {
                if (document.getElementById('payment_type').value === 'installment') {
                    updateInstallmentsList();
                }
            });
        }
    });
</script>
@endpush
