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
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-send-check me-2"></i>Sistema de Referidos
                </h5>
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="referral_enabled" name="referral_enabled" value="1"
                           {{ $referralEnabled == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="referral_enabled">
                        Habilitar sistema de referidos
                    </label>
                </div>
                <small class="text-secondary">Si está desactivado, los pacientes no podrán ver su enlace de referido.</small>
            </div>

            <div class="col-12 col-md-4">
                <label for="referral_bonus_sessions" class="form-label fw-bold">Sesiones extra para el referidor</label>
                <input type="number" class="form-control" id="referral_bonus_sessions"
                       name="referral_bonus_sessions" value="{{ $referralBonusSessions }}"
                       min="1" max="50" placeholder="3">
                <small class="text-secondary">Sesiones gratuitas que recibe el paciente que refiere.</small>
            </div>

            <div class="col-12 col-md-4">
                <label for="referral_commission_type" class="form-label fw-bold">Tipo de comisión (empleada)</label>
                <select class="form-select" id="referral_commission_type" name="referral_commission_type">
                    <option value="fixed" {{ $referralCommissionType === 'fixed' ? 'selected' : '' }}>
                        Pago fijo (COP)
                    </option>
                    <option value="percentage" {{ $referralCommissionType === 'percentage' ? 'selected' : '' }}>
                        Porcentaje (%)
                    </option>
                </select>
                <small class="text-secondary">La comisión se asigna a la última empleada que atendió al referidor.</small>
            </div>

            <div class="col-12 col-md-4">
                <label for="referral_commission_value" class="form-label fw-bold">Valor de la comisión</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="referral_commission_value"
                           name="referral_commission_value" value="{{ $referralCommissionValue }}"
                           min="0" step="0.01" placeholder="0">
                    <span class="input-group-text" id="commission-suffix">
                        {{ $referralCommissionType === 'percentage' ? '%' : 'COP' }}
                    </span>
                </div>
                <small class="text-secondary">Ingresa 0 para desactivar la comisión de empleada.</small>
            </div>

            {{-- Sección Empleados --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="bi bi-person-badge me-2"></i>Staff y Empleadas
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="staff_commission_target" class="form-label fw-bold">Meta mensual de comisiones</label>
                <div class="input-group">
                    <span class="input-group-text">COP</span>
                    <input type="number" class="form-control" id="staff_commission_target" name="staff_commission_target"
                           value="{{ $staffCommissionTarget }}" min="0" step="1000">
                </div>
                <small class="text-secondary">Si alcanzan la meta, su comisión aumenta de 2% a 4%.</small>
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
</x-admin-card>

@push('scripts')
<script>
    document.getElementById('referral_commission_type').addEventListener('change', function() {
        const suffix = document.getElementById('commission-suffix');
        suffix.textContent = this.value === 'percentage' ? '%' : 'COP';
    });
</script>
@endpush
@endsection
