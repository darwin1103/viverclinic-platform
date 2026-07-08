@extends('layouts.admin')

@section('content')
<x-admin-card title="Configuración">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">

            {{-- Sección Wompi --}}
            <div class="col-12">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-credit-card me-2"></i>Pasarela de Pagos (Wompi)
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="wompi_public_key" class="form-label fw-bold">Wompi Public Key</label>
                <input type="text" class="form-control" id="wompi_public_key" name="wompi_public_key"
                       value="{{ $wompiPublicKey }}" placeholder="pub_prod_...">
            </div>

            <div class="col-12 col-md-6">
                <label for="wompi_integrity_secret" class="form-label fw-bold">Wompi Integrity Secret</label>
                <input type="text" class="form-control" id="wompi_integrity_secret" name="wompi_integrity_secret"
                       value="{{ $wompiIntegritySecret }}" placeholder="prod_integrity_...">
            </div>

            {{-- Sección Referidos --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-info">
                    <i class="bi bi-send-check me-2"></i>Sistema de Referidos
                </h5>
            </div>

            <div class="col-12 mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="referral_enabled" name="referral_enabled" value="1"
                           {{ $referralEnabled == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-white" for="referral_enabled">
                        Habilitar sistema de referidos
                    </label>
                </div>
                <small class="text-secondary">Si está desactivado, los pacientes no podrán ver su enlace de referido.</small>
            </div>

            <div class="col-12 col-md-4">
                <label for="referral_bonus_sessions" class="form-label fw-bold text-white">Sesiones extra para el referidor</label>
                <input type="number" class="form-control bg-dark text-white border-secondary" id="referral_bonus_sessions"
                       name="referral_bonus_sessions" value="{{ $referralBonusSessions }}"
                       min="1" max="50" placeholder="3">
                <small class="text-secondary">Sesiones gratuitas que recibe el paciente que refiere.</small>
            </div>

            <div class="col-12 mt-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="referral_sales_enabled" name="referral_sales_enabled" value="1"
                           {{ $referralSalesEnabled == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-white" for="referral_sales_enabled">
                        Registrar referidos como ventas
                    </label>
                </div>
                <small class="text-secondary">Si está activado, los pagos de referidos se registrarán como ventas para la empleada.</small>
            </div>

            {{-- Sección Agrandamientos --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-warning">
                    <i class="bi bi-arrow-up-right-circle me-2"></i>Sistema de Agrandamientos
                </h5>
            </div>

            <div class="col-12 mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="upgrade_sales_enabled" name="upgrade_sales_enabled" value="1"
                           {{ $upgradeSalesEnabled == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-white" for="upgrade_sales_enabled">
                        Registrar agrandamientos como ventas
                    </label>
                </div>
                <small class="text-secondary">Si está activado, se registrará la venta para la empleada que atendió la primera cita.</small>
            </div>

            {{-- Sección Recompras --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">
                    <i class="bi bi-arrow-repeat me-2"></i>Sistema de Recompras
                </h5>
            </div>

            <div class="col-12 mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="repurchase_sales_enabled" name="repurchase_sales_enabled" value="1"
                           {{ $repurchaseSalesEnabled == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-white" for="repurchase_sales_enabled">
                        Registrar recompras como ventas
                    </label>
                </div>
                <small class="text-secondary">Si está activado, las compras de nuevos paquetes por pacientes existentes contarán como venta para la última empleada que lo atendió.</small>
            </div>

            {{-- Meta Global de Ventas --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-success">
                    <i class="bi bi-trophy me-2"></i>Meta Global de Ventas
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="commission_target" class="form-label fw-bold text-white">Meta mensual de ventas (Monto)</label>
                <div class="input-group">
                    <span class="input-group-text bg-secondary text-white border-secondary">COP</span>
                    <input type="text" inputmode="numeric" class="form-control bg-dark text-white border-secondary currency-input" id="commission_target" name="commission_target"
                           value="{{ $commissionTarget }}">
                </div>
                <small class="text-secondary">Meta unificada de ventas en dinero para visualizar el progreso del equipo.</small>
            </div>
            <div class="col-12 col-md-6">
                <label for="commission_target_count" class="form-label fw-bold text-white">Meta mensual de ventas (Cantidad)</label>
                <div class="input-group">
                    <input type="number" class="form-control bg-dark text-white border-secondary" id="commission_target_count" name="commission_target_count"
                           value="{{ $commissionTargetCount }}">
                    <span class="input-group-text bg-secondary text-white border-secondary">ventas</span>
                </div>
                <small class="text-secondary">Meta unificada de cantidad de ventas para visualizar el progreso del equipo.</small>
            </div>

            {{-- Sección Pagos --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-success">
                    <i class="bi bi-wallet2 me-2"></i>Configuración de Pagos
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="minimum_abono_amount" class="form-label fw-bold text-white">Monto Mínimo de Abono (COP)</label>
                <input type="text" inputmode="numeric" class="form-control bg-dark text-white border-secondary currency-input" id="minimum_abono_amount"
                       name="minimum_abono_amount" value="{{ $minimumAbonoAmount }}"
                       placeholder="50000">
                <small class="text-secondary">Monto mínimo en pesos (COP) que un paciente puede abonar a un tratamiento.</small>
            </div>

            {{-- Sección Disparos --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-bullseye me-2"></i>Control de Disparos
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="shots_per_zone" class="form-label fw-bold">Límite de disparos por Zona</label>
                <input type="number" class="form-control" id="shots_per_zone" name="shots_per_zone"
                       value="{{ $shotsPerZone }}" min="1" placeholder="600">
                <small class="text-secondary">Límite global aplicable a zonas grandes y pequeñas.</small>
            </div>

            <div class="col-12 col-md-6">
                <label for="shots_per_minizone" class="form-label fw-bold">Límite de disparos por Mini-zona</label>
                <input type="number" class="form-control" id="shots_per_minizone" name="shots_per_minizone"
                       value="{{ $shotsPerMinizone }}" min="1" placeholder="200">
                <small class="text-secondary">Límite global aplicable a mini-zonas.</small>
            </div>

            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i>Guardar Cambios
                </button>
            </div>

        </div>
    </form>

    {{-- Sección Días Festivos movida a configuración de agenda --}}

</x-admin-card>
@endsection
