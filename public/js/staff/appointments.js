document.addEventListener('DOMContentLoaded', function () {

    // --- Debounce Function ---
    /**
     * Creates a debounced version of a function that delays its execution.
     * @param {Function} func The function to debounce.
     * @param {number} delay The delay in milliseconds.
     * @returns {Function} The debounced function.
     */
    const debounce = (func, delay = 500) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    };

    // --- Filter Logic ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const treatmentSelect = document.getElementById('treatment_id');
    const minDateInput = document.getElementById('min_date');
    const maxDateInput = document.getElementById('max_date');

    /**
     * Submits the filter form.
     */
    const submitFilterForm = () => {
        filterForm.submit();
    };

    // Create a debounced version of the form submission
    const debouncedSubmit = debounce(submitFilterForm);

    if (filterForm) {
        // Apply debounce to text and date inputs to avoid too many requests
        [searchInput, minDateInput, maxDateInput].forEach(input => {
            if (input) {
                input.addEventListener('input', debouncedSubmit);
            }
        });

        // Submit immediately on select change
        if (treatmentSelect) {
            treatmentSelect.addEventListener('change', submitFilterForm);
        }
    }


    // --- Modal Logic ---
    const appointmentModal = document.getElementById('appointmentActionModal');

    if (appointmentModal) {

        appointmentModal.addEventListener('input', function (event) {

            // Verificamos si el elemento que cambió es uno de nuestros inputs
            if (event.target && (event.target.id === 'initial-shots' || event.target.id === 'final-shots')) {
                calculateShots();
            }
        });


        appointmentModal.addEventListener('show.bs.modal', function (event) {
            // Botón que disparó el modal
            const button = event.relatedTarget;

            // Extraer información de los atributos data-*
            const patientName = button.getAttribute('data-patient-name');

            // Parsear los objetos JSON de los atributos
            const details = JSON.parse(button.getAttribute('data-appointment-details'));
            const zonesString = button.getAttribute('data-zones');
            let zones = null;
            if (zonesString) {
                try {
                    zones = JSON.parse(zonesString);
                } catch (e) {
                    console.error("Error al parsear las zonas:", e);
                }
            }

            const shots = button.getAttribute('data-shots');
            const shotsUrl = button.getAttribute('data-set-appointment-shots-url');
            const markAsCompletedUrl = button.getAttribute('data-set-mark-as-completed-url');

            // Seleccionar los elementos del modal
            const modalTitle = appointmentModal.querySelector('.modal-title');
            const modalBody = appointmentModal.querySelector('.modal-body');

            // Actualizar el título del modal
            modalTitle.textContent = `Detalles de la Cita de: ${patientName}`;

            // Generar HTML para los sub-paquetes
            function getStatusVariant(status) {
                const map = {
                    'Atendida': 'primary',
                    'Completada': 'success',
                    'Confirmada': 'success',
                    'Por confirmar': 'warning',
                    'Agendada': 'info',
                    'Cancelada': 'danger',
                    'No asistida': 'danger',
                    'Pendiente': 'secondary'
                };
                return map[status] || 'secondary';
            }

            const reviewValues = {
                1: '😡 Muy Malo',
                2: '🙁 Malo',
                3: '😐 Regular',
                4: '🙂 Bueno',
                5: '🤩 Excelente'
            };

            const subAppointmentsHtml = `
                <div class="mt-3">
                    <strong>Paquetes Incluidos:</strong>
                    ${(details.sub_appointments || []).map(sub => `
                    <div class="mt-2 p-3 border border-secondary rounded position-relative" style="background: rgba(0,0,0,0.1);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-primary">${sub.treatment}</strong>
                            <span class="badge text-bg-${getStatusVariant(sub.status)}">${sub.status === 'Atendida' ? 'Atención' : sub.status}</span>
                        </div>
                        <div class="small">
                            <div class="mb-1"><strong class="text-secondary">Sesión:</strong> #${sub.session_number}</div>
                            
                            ${(sub.zones?.big?.length > 0) ? `
                                <div class="mb-1">
                                    <strong class="text-secondary">Zonas grandes:</strong>
                                    ${sub.zones.big.join(', ')}
                                </div>
                            ` : ''}

                            ${(sub.zones?.mini?.length > 0) ? `
                                <div class="mb-1">
                                    <strong class="text-secondary">Mini zonas:</strong>
                                    ${sub.zones.mini.join(', ')}
                                </div>
                            ` : ''}
                            
                            ${(!['Pendiente', 'Agendado', 'Por confirmar'].includes(sub.status) || sub.review_score) ? `<div class="mb-1"><strong class="text-secondary">Calificación:</strong> ${sub.review_score ? (reviewValues[sub.review_score] + (sub.review ? (' - ' + sub.review) : '')) : 'N/A'}</div>` : ''}
                        </div>
                    </div>
                    `).join('')}
                </div>
            `;

            let markAsCompletedForm = '';

            let shotsHtml = '';
            const shotsNumber = parseInt(shots, 10);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Condición 2: Si es un número entero igual a cero (necesita reporte de disparos)
            if (shotsNumber === 0) {
                shotsHtml = `
                <div class="mt-3">
                    <strong>Cantidad de disparos en cabina:</strong>
                    <div class="input-group mt-2">
                        <input type="number" id="initial-shots" class="form-control" placeholder="Disparos (inicial)" min="1" aria-label="Disparos (inicial)" required>
                        <input type="number" id="final-shots" class="form-control" placeholder="Disparos (final)" min="1" aria-label="Disparos (final)" required>
                        <input type="number" id="shots" readonly name="shots" class="form-control" placeholder="Disparos (total)" min="1" aria-label="Disparos (total)" required>
                    </div>
                     <div class="invalid-feedback">
                        Por favor, ingrese un número de disparos mayor a cero.
                    </div>
                </div>
                `;
            }
            // Condición 3: Si es un número entero mayor a cero (ya reportado).
            else if (shotsNumber > 0) {
                shotsHtml = `<div class="mt-3"><strong>Disparos en cabina:</strong> <span class="badge bg-success">${shots}</span></div>`;
            }

            if (markAsCompletedUrl ) {

                markAsCompletedForm = `
                <div class="mt-4 border-top pt-3">
                    <form action="${markAsCompletedUrl}" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="_token" value="${csrfToken}">
                        ${shotsNumber === 0 ? shotsHtml : ''}
                        <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-check-circle me-2"></i>Marcar como completada</button>
                    </form>
                </div>
                `;
                // Si la marcamos como completada, el shotsHtml va DENTRO del form para que lo envíe.
                // Limpiamos la variable global de shotsHtml para que no se imprima dos veces.
                if (shotsNumber === 0) {
                    shotsHtml = ''; 
                }
            }

            // Construir el cuerpo completo del modal
            modalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card bg-transparent">
                            <div class="card-body">
                                <h6 class="text-secondary small text-uppercase mb-3">Detalles de la Cita</h6>
                                <div class="vstack gap-2">
                                    <div><strong>Tratamiento General:</strong> ${details.treatment}</div>
                                    <div><strong>Fecha:</strong> ${details.date}</div>
                                    <div><strong>Hora:</strong> ${details.time}</div>
                                    <div><strong>Estado Global:</strong> <span class="badge ${details.status === 'Atendida' ? 'text-bg-primary' : 'text-bg-info'}">${details.status === 'Atendida' ? 'Atención' : details.status}</span></div>
                                    ${subAppointmentsHtml}
                                    ${shotsHtml}
                                    ${markAsCompletedForm}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function calculateShots() {
        const initialInput = document.getElementById('initial-shots');
        const finalInput = document.getElementById('final-shots');
        const totalInput = document.getElementById('shots');

        // Aseguramos que los elementos existan antes de calcular
        if (initialInput && finalInput && totalInput) {
            const initial = parseFloat(initialInput.value) || 0;
            const final = parseFloat(finalInput.value) || 0;

            // Calculamos la resta
            const result = final - initial;

            // Mostramos el resultado (opcional: evitar negativos)
            totalInput.value = result > 0 ? result : 0;
        }
    }

});
