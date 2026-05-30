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
                    <div class="col-12">
                        <label class="form-label fw-bold text-white mb-2">Paquetes Contratados <span class="text-danger">*</span></label>
                        <input type="hidden" name="contracted_treatment_id" id="selectedContractedTreatmentId" required>
                        <div id="treatmentsTableContainer" style="display: none;">
                            <div class="table-responsive border border-secondary rounded">
                                <table class="table table-dark table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-success border-secondary" style="width: 40px;"></th>
                                            <th class="text-white border-secondary">Paquete / Tratamiento</th>
                                            <th class="text-white border-secondary text-center">F. Contratación</th>
                                            <th class="text-white border-secondary text-center">Estado</th>
                                            <th class="text-white border-secondary text-center">Forma de Pago</th>
                                            <th class="text-white border-secondary text-end">Saldo Pendiente</th>
                                        </tr>
                                    </thead>
                                    <tbody id="treatmentsTableBody">
                                        <!-- Se llena por JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="noTreatmentsWarning" class="alert alert-secondary border-secondary mb-0 bg-dark text-white-50">
                            <i class="bi bi-info-circle me-2"></i>Seleccione primero un paciente para ver sus paquetes contratados.
                        </div>
                        @error('contracted_treatment_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12" id="installmentsContainer" style="display: none;">
                        <label class="form-label fw-semibold text-primary"><i class="bi bi-list-check me-1"></i> Seleccionar Cuotas a Pagar</label>
                        <div id="installmentsList" class="d-flex flex-wrap gap-3 py-2">
                            <!-- Se llena por JS -->
                        </div>
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
    const hiddenContractedTreatmentId = document.getElementById('selectedContractedTreatmentId');
    const selectedBadge = document.getElementById('selectedPatientBadge');
    const selectedName = document.getElementById('selectedPatientName');
    const installmentsContainer = document.getElementById('installmentsContainer');
    const installmentsList = document.getElementById('installmentsList');
    const totalInput = document.querySelector('input[name="total"]');
    
    let currentTreatmentsData = [];

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
        hiddenContractedTreatmentId.value = '';
        
        document.getElementById('treatmentsTableBody').innerHTML = '';
        document.getElementById('treatmentsTableContainer').style.display = 'none';
        document.getElementById('noTreatmentsWarning').style.display = 'block';
        document.getElementById('noTreatmentsWarning').innerHTML = '<i class="bi bi-info-circle me-2"></i>Seleccione primero un paciente para ver sus paquetes contratados.';
        
        installmentsContainer.style.display = 'none';
        installmentsList.innerHTML = '';
        totalInput.value = '';
        totalInput.removeAttribute('max');
        totalInput.readOnly = false;
    };

    function selectTreatment(treatmentId) {
        const treatment = currentTreatmentsData.find(t => t.id == treatmentId);
        hiddenContractedTreatmentId.value = treatmentId;
        
        installmentsList.innerHTML = '';
        if (treatment) {
            // Apply select styling to the active row
            document.querySelectorAll('#treatmentsTableBody tr').forEach(row => {
                const radio = row.querySelector('.treatment-radio');
                if (radio && radio.checked) {
                    row.classList.add('table-active');
                } else {
                    row.classList.remove('table-active');
                }
            });

            if (treatment.payment_type === 'abono') {
                installmentsContainer.style.display = 'block';
                installmentsList.innerHTML = `
                    <div class="alert alert-info w-100 mb-0 border-0 bg-info-subtle text-info-emphasis">
                        <i class="bi bi-info-circle me-1"></i> Este paquete se encuentra en modalidad de <strong>Abonos</strong>.<br>
                        <strong>Saldo Restante:</strong> $${parseFloat(treatment.remaining_balance).toLocaleString('es-CO')}<br>
                        <strong>Precio Total:</strong> $${parseFloat(treatment.total_price).toLocaleString('es-CO')}
                    </div>
                `;
                totalInput.value = treatment.remaining_balance;
                totalInput.max = treatment.remaining_balance;
                totalInput.readOnly = false;
            } else if (treatment.payment_type === 'full') {
                installmentsContainer.style.display = 'block';
                installmentsList.innerHTML = `
                    <div class="alert alert-warning w-100 mb-0 border-0 bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-info-circle me-1"></i> Este paquete se encuentra en modalidad de <strong>Pago Único</strong>.<br>
                        <strong>Saldo Restante:</strong> $${parseFloat(treatment.remaining_balance).toLocaleString('es-CO')}
                    </div>
                `;
                totalInput.value = treatment.remaining_balance;
                totalInput.max = treatment.remaining_balance;
                totalInput.readOnly = false;
            } else if (treatment.installments && treatment.installments.length > 0) {
                installmentsContainer.style.display = 'block';
                treatment.installments.forEach(inst => {
                    const div = document.createElement('div');
                    div.className = 'form-check form-check-inline';
                    div.innerHTML = `
                        <input class="form-check-input installment-checkbox" type="checkbox" name="paid_installments_ids[]" value="${inst.id}" id="inst_${inst.id}" data-price="${inst.price}">
                        <label class="form-check-label text-white" for="inst_${inst.id}">
                            ${inst.label}
                        </label>
                    `;
                    installmentsList.appendChild(div);
                });
                
                // Add listener to checkboxes to update total
                document.querySelectorAll('.installment-checkbox').forEach(cb => {
                    cb.addEventListener('change', calculateTotal);
                });
                totalInput.value = '';
                totalInput.removeAttribute('max');
                totalInput.readOnly = true;
            } else {
                installmentsContainer.style.display = 'block';
                installmentsList.innerHTML = `
                    <div class="alert alert-success w-100 mb-0 border-0 bg-success-subtle text-success-emphasis">
                        <i class="bi bi-check-circle me-1"></i> El paquete ya se encuentra totalmente pagado o no tiene cuotas pendientes.<br>
                        <strong>Saldo Restante:</strong> $${parseFloat(treatment.remaining_balance).toLocaleString('es-CO')}
                    </div>
                `;
                totalInput.value = treatment.remaining_balance;
                totalInput.max = treatment.remaining_balance;
                totalInput.readOnly = false;
            }
        } else {
            installmentsContainer.style.display = 'none';
            totalInput.value = '';
            totalInput.removeAttribute('max');
            totalInput.readOnly = false;
        }
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.installment-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.dataset.price);
        });
        if (total > 0) {
            totalInput.value = total;
        } else {
            totalInput.value = '';
        }
    }

    function loadTreatments(patientId) {
        if (!patientId) return;

        const body = document.getElementById('treatmentsTableBody');
        body.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Cargando paquetes...</td></tr>';
        document.getElementById('noTreatmentsWarning').style.display = 'none';
        document.getElementById('treatmentsTableContainer').style.display = 'block';

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
            body.innerHTML = '';
            if (data.length === 0) {
                document.getElementById('treatmentsTableContainer').style.display = 'none';
                document.getElementById('noTreatmentsWarning').style.display = 'block';
                document.getElementById('noTreatmentsWarning').innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Este paciente no tiene paquetes contratados activos.';
            } else {
                currentTreatmentsData = data;
                document.getElementById('noTreatmentsWarning').style.display = 'none';
                document.getElementById('treatmentsTableContainer').style.display = 'block';

                data.forEach(t => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    
                    let pType = 'Desconocido';
                    if (t.payment_type === 'installment') pType = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Cuotas</span>';
                    else if (t.payment_type === 'abono') pType = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Abonos</span>';
                    else if (t.payment_type === 'full') pType = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Pago Único</span>';

                    let statusBadge = '';
                    if (t.status === 'Paid' || t.status === 'Pagado') statusBadge = '<span class="badge bg-success">Pagado</span>';
                    else if (t.status === 'Pending' || t.status === 'Pendiente') statusBadge = '<span class="badge bg-warning text-dark">Pendiente</span>';
                    else statusBadge = `<span class="badge bg-secondary">${t.status}</span>`;

                    tr.innerHTML = `
                        <td class="text-center border-secondary">
                            <input class="form-check-input treatment-radio" type="radio" name="temp_contracted_treatment_id" value="${t.id}" id="radio_ct_${t.id}">
                        </td>
                        <td class="border-secondary text-white">
                            <label for="radio_ct_${t.id}" class="fw-semibold cursor-pointer m-0">${t.name}</label>
                        </td>
                        <td class="border-secondary text-center text-white-50">
                            ${t.created_at_formatted || '-'}
                        </td>
                        <td class="border-secondary text-center">
                            ${statusBadge}
                        </td>
                        <td class="border-secondary text-center">
                            ${pType}
                        </td>
                        <td class="border-secondary text-end fw-bold text-white">
                            $${parseFloat(t.remaining_balance).toLocaleString('es-CO')}
                        </td>
                    `;

                    // Add click event listener to the row to select the radio button
                    tr.addEventListener('click', () => {
                        const radio = tr.querySelector('.treatment-radio');
                        if (radio) {
                            radio.checked = true;
                            selectTreatment(t.id);
                        }
                    });

                    body.appendChild(tr);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching treatments:', error);
            body.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3"><i class="bi bi-x-circle me-2"></i>Error al cargar paquetes contratados.</td></tr>';
        });
    }
});
</script>
@endpush

@endsection
