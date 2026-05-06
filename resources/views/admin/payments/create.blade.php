@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Registrar Pago</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold">
            <span><i class="bi bi-cash-coin me-2"></i>Nuevo Pago</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payments.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Paciente <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="patientSearch" placeholder="Buscar paciente por nombre..." autocomplete="off">
                            <input type="hidden" name="user_id" id="selectedPatientId" value="{{ old('user_id') }}">
                            <div id="patientDropdown" class="dropdown-menu w-100 shadow" style="max-height: 250px; overflow-y: auto;"></div>
                        </div>
                        <div id="selectedPatientBadge" class="mt-2" style="display: none;">
                            <span class="badge bg-primary fs-6 d-inline-flex align-items-center gap-2">
                                <span id="selectedPatientName"></span>
                                <button type="button" class="btn-close btn-close-white" style="font-size: 0.6rem;" onclick="clearPatient()"></button>
                            </span>
                        </div>
                        @error('user_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tratamiento Contratado <span class="text-danger">*</span></label>
                        <select class="form-select @error('contracted_treatment_id') is-invalid @enderror" name="contracted_treatment_id" id="treatmentSelect" required disabled>
                            <option value="">Seleccione primero un paciente</option>
                        </select>
                        @error('contracted_treatment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Monto <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total" class="form-control @error('total') is-invalid @enderror" value="{{ old('total') }}" placeholder="Ej. 10000" required>
                        @error('total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                            <option value="">Seleccione método</option>
                            <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="Tarjeta de Crédito" {{ old('payment_method') == 'Tarjeta de Crédito' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                            <option value="Tarjeta de Débito" {{ old('payment_method') == 'Tarjeta de Débito' ? 'selected' : '' }}>Tarjeta de Débito</option>
                            <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patients = @json($patients);
    const searchInput = document.getElementById('patientSearch');
    const dropdown = document.getElementById('patientDropdown');
    const hiddenInput = document.getElementById('selectedPatientId');
    const treatmentSelect = document.getElementById('treatmentSelect');
    const selectedBadge = document.getElementById('selectedPatientBadge');
    const selectedName = document.getElementById('selectedPatientName');

    // Patient search functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';

        if (query.length < 2) {
            dropdown.classList.remove('show');
            return;
        }

        const filtered = patients.filter(p => p.name.toLowerCase().includes(query)).slice(0, 15);

        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="dropdown-item text-muted">Sin resultados</div>';
        } else {
            filtered.forEach(patient => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'dropdown-item';
                item.textContent = patient.name;
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectPatient(patient);
                });
                dropdown.appendChild(item);
            });
        }
        dropdown.classList.add('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    function selectPatient(patient) {
        hiddenInput.value = patient.id;
        selectedName.textContent = patient.name;
        selectedBadge.style.display = 'block';
        searchInput.value = '';
        searchInput.style.display = 'none';
        dropdown.classList.remove('show');
        loadTreatments(patient.id);
    }

    window.clearPatient = function() {
        hiddenInput.value = '';
        selectedBadge.style.display = 'none';
        searchInput.style.display = 'block';
        searchInput.value = '';
        treatmentSelect.innerHTML = '<option value="">Seleccione primero un paciente</option>';
        treatmentSelect.disabled = true;
    };

    function loadTreatments(patientId) {
        if (!patientId) return;

        treatmentSelect.innerHTML = '<option value="">Cargando...</option>';
        treatmentSelect.disabled = true;

        // Use route helper with placeholder
        const url = "{{ route('admin.patients.treatments', ':id') }}".replace(':id', patientId);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            treatmentSelect.innerHTML = '<option value="">Seleccione un tratamiento</option>';
            if (data.length === 0) {
                treatmentSelect.innerHTML = '<option value="">Este paciente no tiene tratamientos activos</option>';
            } else {
                data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.name;
                    treatmentSelect.appendChild(opt);
                });
                treatmentSelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error fetching treatments:', error);
            treatmentSelect.innerHTML = '<option value="">Error al cargar tratamientos</option>';
        });
    }
});
</script>
@endpush

@endsection
