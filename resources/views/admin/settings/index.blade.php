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

            <div class="col-12 col-md-4">
                <label for="referral_commission_type" class="form-label fw-bold text-white">Tipo de comisión (empleada)</label>
                <select class="form-select bg-dark text-white border-secondary" id="referral_commission_type" name="referral_commission_type">
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
                <label for="referral_commission_value" class="form-label fw-bold text-white">Valor de la comisión</label>
                <div class="input-group">
                    <input type="text" inputmode="{{ $referralCommissionType === 'percentage' ? 'decimal' : 'numeric' }}" class="form-control bg-dark text-white border-secondary {{ $referralCommissionType === 'fixed' ? 'currency-input' : '' }}" id="referral_commission_value"
                           name="referral_commission_value" value="{{ $referralCommissionValue }}"
                           placeholder="0">
                    <span class="input-group-text bg-secondary text-white border-secondary" id="commission-suffix">
                        {{ $referralCommissionType === 'percentage' ? '%' : 'COP' }}
                    </span>
                </div>
                <small class="text-secondary">Ingresa 0 para desactivar la comisión de empleada.</small>
            </div>

            {{-- Sección Agrandamientos --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-warning">
                    <i class="bi bi-arrow-up-right-circle me-2"></i>Sistema de Agrandamientos
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="upgrade_commission_type" class="form-label fw-bold text-white">Tipo de comisión (empleada)</label>
                <select class="form-select bg-dark text-white border-secondary" id="upgrade_commission_type" name="upgrade_commission_type">
                    <option value="fixed" {{ $upgradeCommissionType === 'fixed' ? 'selected' : '' }}>
                        Pago fijo (COP)
                    </option>
                    <option value="percentage" {{ $upgradeCommissionType === 'percentage' ? 'selected' : '' }}>
                        Porcentaje (%)
                    </option>
                </select>
                <small class="text-secondary">Tipo de comisión asignada por agrandamiento de paquete.</small>
            </div>

            <div class="col-12 col-md-6">
                <label for="upgrade_commission_value" class="form-label fw-bold text-white">Valor de la comisión</label>
                <div class="input-group">
                    <input type="text" inputmode="{{ $upgradeCommissionType === 'percentage' ? 'decimal' : 'numeric' }}" class="form-control bg-dark text-white border-secondary {{ $upgradeCommissionType === 'fixed' ? 'currency-input' : '' }}" id="upgrade_commission_value"
                           name="upgrade_commission_value" value="{{ $upgradeCommissionValue }}"
                           placeholder="0">
                    <span class="input-group-text bg-secondary text-white border-secondary" id="upgrade-commission-suffix">
                        {{ $upgradeCommissionType === 'percentage' ? '%' : 'COP' }}
                    </span>
                </div>
                <small class="text-secondary">Se calcula sobre la diferencia pagada por el cliente.</small>
            </div>

            {{-- Sección Recompras --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">
                    <i class="bi bi-arrow-repeat me-2"></i>Sistema de Recompras
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="repurchase_commission_type" class="form-label fw-bold text-white">Tipo de comisión (empleada)</label>
                <select class="form-select bg-dark text-white border-secondary" id="repurchase_commission_type" name="repurchase_commission_type">
                    <option value="fixed" {{ $repurchaseCommissionType === 'fixed' ? 'selected' : '' }}>
                        Pago fijo (COP)
                    </option>
                    <option value="percentage" {{ $repurchaseCommissionType === 'percentage' ? 'selected' : '' }}>
                        Porcentaje (%)
                    </option>
                </select>
                <small class="text-secondary">Comisión asignada a la última empleada que atendió al paciente antes de la recompra.</small>
            </div>

            <div class="col-12 col-md-6">
                <label for="repurchase_commission_value" class="form-label fw-bold text-white">Valor de la comisión</label>
                <div class="input-group">
                    <input type="text" inputmode="{{ $repurchaseCommissionType === 'percentage' ? 'decimal' : 'numeric' }}" class="form-control bg-dark text-white border-secondary {{ $repurchaseCommissionType === 'fixed' ? 'currency-input' : '' }}" id="repurchase_commission_value"
                           name="repurchase_commission_value" value="{{ $repurchaseCommissionValue }}"
                           placeholder="0">
                    <span class="input-group-text bg-secondary text-white border-secondary" id="repurchase-commission-suffix">
                        {{ $repurchaseCommissionType === 'percentage' ? '%' : 'COP' }}
                    </span>
                </div>
                <small class="text-secondary">Ingresa 0 para desactivar. Si es %, se calcula sobre el primer pago del paciente.</small>
            </div>

            {{-- Meta Global de Comisiones --}}
            <div class="col-12 mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-success">
                    <i class="bi bi-trophy me-2"></i>Meta Global de Comisiones
                </h5>
            </div>

            <div class="col-12 col-md-6">
                <label for="commission_target" class="form-label fw-bold text-white">Meta mensual unificada</label>
                <div class="input-group">
                    <span class="input-group-text bg-secondary text-white border-secondary">COP</span>
                    <input type="text" inputmode="numeric" class="form-control bg-dark text-white border-secondary currency-input" id="commission_target" name="commission_target"
                           value="{{ $commissionTarget }}">
                </div>
                <small class="text-secondary">Meta unificada que incluye comisiones por referidos, agrandamientos y recompras.</small>
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

    {{-- Sección Días Festivos (formulario independiente) --}}
    <hr class="my-4">
    <div class="row g-3">
        <div class="col-12">
            <h5 class="fw-bold border-bottom pb-2 mb-3">
                <i class="bi bi-calendar-x me-2"></i>Días Festivos
            </h5>
            <small class="text-secondary d-block mb-3">
                Los días registrados aquí se bloquearán automáticamente en todos los calendarios de agendamiento. Los pacientes y administradores no podrán agendar citas en estas fechas.
            </small>
        </div>

        {{-- Add new holiday --}}
        <div class="col-12">
            <form action="{{ route('admin.holidays.store') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-12 col-md-4">
                    <label for="holiday_date" class="form-label fw-bold">Fecha</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                           id="holiday_date" name="date" value="{{ old('date') }}" required>
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-5">
                    <label for="holiday_name" class="form-label fw-bold">Motivo</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="holiday_name" name="name" value="{{ old('name') }}" placeholder="Ej: Navidad, Año Nuevo..." required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Festivo
                    </button>
                </div>
            </form>
        </div>

        {{-- Holidays list --}}
        <div class="col-12 mt-3">
            @php
                $allHolidays = \App\Models\Holiday::orderBy('date', 'asc')->get();
            @endphp

            @if($allHolidays->isEmpty())
                <div class="text-secondary text-center py-3">
                    <i class="bi bi-calendar-check me-1"></i>No hay días festivos registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Motivo</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allHolidays as $holiday)
                                <tr>
                                    <td>
                                        <span class="badge {{ $holiday->date->isPast() ? 'bg-secondary' : 'bg-danger' }}">
                                            {{ $holiday->date->isoFormat('DD MMM YYYY') }}
                                        </span>
                                    </td>
                                    <td>{{ $holiday->name }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar este día festivo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</x-admin-card>

@push('scripts')
<script>
    function setupCommissionToggle(typeSelectId, valueInputId, suffixId) {
        const typeSelect = document.getElementById(typeSelectId);
        const valueInput = document.getElementById(valueInputId);
        const suffix = document.getElementById(suffixId);

        function applyMode() {
            const isPercentage = typeSelect.value === 'percentage';
            suffix.textContent = isPercentage ? '%' : 'COP';

            if (isPercentage) {
                // Only strip dots if transitioning FROM fixed mode (dots are thousand separators)
                const wasCurrency = valueInput.classList.contains('currency-input');
                valueInput.classList.remove('currency-input');
                valueInput.setAttribute('inputmode', 'decimal');
                if (wasCurrency) {
                    // Dots were thousand separators, strip them
                    valueInput.value = valueInput.value.replace(/\./g, '') || '';
                }
                // If not wasCurrency, value is already a decimal (e.g. "3.33") — leave it
            } else {
                // Switch to currency mode: strip non-digits, re-apply formatting
                let raw = valueInput.value.replace(/[^\d]/g, '');
                valueInput.classList.add('currency-input');
                valueInput.setAttribute('inputmode', 'numeric');
                // Re-apply thousand separator formatting
                valueInput.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }

        // Apply on page load
        applyMode();

        // Apply on change
        typeSelect.addEventListener('change', applyMode);

        // For percentage mode, allow only digits and one decimal point
        valueInput.addEventListener('input', function () {
            if (typeSelect.value === 'percentage') {
                // Allow digits and at most one decimal separator (dot or comma → dot)
                let val = this.value.replace(',', '.');
                // Remove anything that is not a digit or dot
                val = val.replace(/[^\d.]/g, '');
                // Keep only the first dot
                let parts = val.split('.');
                if (parts.length > 2) {
                    val = parts[0] + '.' + parts.slice(1).join('');
                }
                if (this.value !== val) {
                    this.value = val;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupCommissionToggle('referral_commission_type', 'referral_commission_value', 'commission-suffix');
        setupCommissionToggle('upgrade_commission_type', 'upgrade_commission_value', 'upgrade-commission-suffix');
        setupCommissionToggle('repurchase_commission_type', 'repurchase_commission_value', 'repurchase-commission-suffix');
    });
</script>
@endpush
@endsection
