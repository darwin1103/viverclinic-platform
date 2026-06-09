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
            <form action="{{ route('admin.appointments.store') }}" method="POST" id="appointmentForm">
                @csrf
                <input type="hidden" name="date" id="appointmentDateInput" value="{{ old('date') }}">
                <input type="hidden" name="time" id="appointmentTimeInput" value="{{ old('time') }}">
                <input type="hidden" name="branch_id" id="branchIdInput">

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
                        @error('date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @error('time')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Calendar and Slots UI -->
                <div id="calendarSection" class="row g-4 mt-2" style="display: none;">
                    <div class="col-12">
                        <hr>
                        <h5 class="fw-semibold mb-3"><i class="bi bi-calendar-check me-2"></i>Selecciona fecha y hora</h5>
                    </div>
                    <!-- Calendar Section -->
                    <div class="col-12 col-lg-7">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <div class="calendar-header mb-3">
                                    <button type="button" class="btn btn-outline-light btn-sm" id="btnPrevMonth">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <div class="text-center flex-fill">
                                        <div class="fw-semibold" id="lblMonthYear">Mes Año</div>
                                        <div class="text-secondary small">Selecciona un día disponible</div>
                                    </div>
                                    <button type="button" class="btn btn-outline-light btn-sm" id="btnNextMonth">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>

                                <div class="calendar-grid text-center small mb-2">
                                    <div class="text-secondary">Lun</div>
                                    <div class="text-secondary">Mar</div>
                                    <div class="text-secondary">Mié</div>
                                    <div class="text-secondary">Jue</div>
                                    <div class="text-secondary">Vie</div>
                                    <div class="text-secondary">Sáb</div>
                                    <div class="text-secondary">Dom</div>
                                </div>

                                <div id="calendarDays" class="calendar-grid"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slots Section -->
                    <div class="col-12 col-lg-5">
                        <div class="card h-100 border">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <div class="fw-semibold">Horarios disponibles:</div>
                                    <div id="lblSelectedDate" class="text-info small">Selecciona un día</div>
                                </div>

                                <div id="slotsContainer" class="vstack gap-2 flex-grow-1"
                                     style="overflow:auto; max-height:340px;">
                                    <div class="text-secondary text-center py-4">
                                        Selecciona un día para ver horarios disponibles
                                    </div>
                                </div>

                                <div class="mt-3 text-end">
                                    <button type="submit" id="btnConfirm" class="btn btn-primary w-100" disabled>
                                        Agendar cita
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

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/client/appointment/schedule/index/appointments.css') }}?v={{ filemtime(public_path('css/client/appointment/schedule/index/appointments.css')) }}">
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
<script src="{{ asset('js/client/appointment/schedule/index/calendar.js') }}?v={{ filemtime(public_path('js/client/appointment/schedule/index/calendar.js')) }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @php
            $treatmentsArray = $contractedTreatments->map(function($c) {
                return [
                    'id' => $c->id,
                    'user_id' => $c->user_id,
                    'branch_id' => $c->branch_id,
                    'text' => ($c->treatment->name ?? 'Tratamiento') . ' (' . $c->status . ')'
                ];
            })->values()->all();
        @endphp
        const treatmentsData = @json($treatmentsArray);

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
            document.getElementById('calendarSection').style.display = 'none';
        });

        // Handle treatment selection to show calendar
        treatmentSelect.on('change', function(treatmentId) {
            const calendarSection = document.getElementById('calendarSection');
            if (treatmentId) {
                const selectedTreatment = treatmentsData.find(t => t.id == treatmentId);
                if (selectedTreatment && selectedTreatment.branch_id) {
                    document.getElementById('branchIdInput').value = selectedTreatment.branch_id;
                    calendarSection.style.display = 'flex';
                    
                    // Delay slightly to ensure calendar elements are visible before rendering
                    setTimeout(() => {
                        CalendarModule.resetSelection();
                        CalendarModule.renderCalendar();
                    }, 50);
                }
            } else {
                calendarSection.style.display = 'none';
                document.getElementById('branchIdInput').value = '';
            }
        });

        // Trigger change on load in case of old input
        if (userSelect.getValue()) {
            userSelect.trigger('change', userSelect.getValue());
            // If there's an old treatment selected, we need to set it back after filtering
            const oldTreatmentId = "{{ old('contracted_treatment_id') }}";
            if (oldTreatmentId) {
                treatmentSelect.setValue(oldTreatmentId);
                treatmentSelect.trigger('change', oldTreatmentId);
            }
        } else {
            // If no user selected initially, clear the treatments list
            treatmentSelect.clearOptions();
            treatmentSelect.clear();
        }
    });
</script>
@endpush
