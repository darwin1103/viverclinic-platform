@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Nueva Cita</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold">
            <span><i class="bi bi-calendar-plus me-2"></i>Programar Cita Manual</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appointments.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Paciente <span class="text-danger">*</span></label>
                        <select id="user_id" class="form-select @error('user_id') is-invalid @enderror" name="user_id">
                            <option value="">Seleccione un paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('user_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }} ({{ $patient->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tratamiento / Paquete Contratado <span class="text-danger">*</span></label>
                        <select id="contracted_treatment_id" class="form-select @error('contracted_treatment_id') is-invalid @enderror" name="contracted_treatment_id" required>
                            <option value="">Seleccione un tratamiento</option>
                            @foreach($contractedTreatments as $contract)
                                <option value="{{ $contract->id }}" {{ old('contracted_treatment_id') == $contract->id ? 'selected' : '' }}>
                                    {{ $contract->treatment->name ?? 'Tratamiento' }} ({{ $contract->status }}) - Paciente: {{ $contract->user->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('contracted_treatment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Hora <span class="text-danger">*</span></label>
                        <input type="time" name="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
                        @error('time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Agendar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Fix for dark mode text in Tom Select */
    [data-bs-theme="dark"] .ts-control {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
    }
    [data-bs-theme="dark"] .ts-dropdown {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
    }
    .ts-control input {
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Prepare treatments data
        const treatmentsData = @json($contractedTreatments->map(function($c) {
            return [
                'id' => $c->id,
                'user_id' => $c->user_id,
                'text' => ($c->treatment->name ?? 'Tratamiento') . ' (' . $c->status . ')'
            ];
        }));

        let treatmentSelect = new TomSelect("#contracted_treatment_id", {
            create: false,
            valueField: 'id',
            labelField: 'text',
            searchField: 'text',
            placeholder: 'Seleccione un tratamiento',
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        let userSelect = new TomSelect("#user_id", {
            create: false,
            placeholder: 'Seleccione un paciente',
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        // Filter treatments when a user is selected
        userSelect.on('change', function(userId) {
            treatmentSelect.clearOptions();
            treatmentSelect.clear();
            if (userId) {
                // Filter treatments belonging to the selected user
                const userTreatments = treatmentsData.filter(t => t.user_id == userId);
                treatmentSelect.addOptions(userTreatments);
            }
        });

        // Trigger change on load in case of old input
        if (userSelect.getValue()) {
            userSelect.trigger('change', userSelect.getValue());
            // If there's an old treatment selected, we need to set it back after filtering
            const oldTreatmentId = "{{ old('contracted_treatment_id') }}";
            if (oldTreatmentId) {
                treatmentSelect.setValue(oldTreatmentId);
            }
        } else {
            // If no user selected initially, clear the treatments list
            treatmentSelect.clearOptions();
            treatmentSelect.clear();
        }
    });
</script>
@endpush
