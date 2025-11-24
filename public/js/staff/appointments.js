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

            // Generar HTML para las zonas (si existen)
            const bigZonesHtml = (zones?.big?.length > 0) ? `
                <div>
                    <strong>Zonas grandes:</strong>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        ${zones.big.map(zone => `<span class="badge bg-secondary">${zone}</span>`).join('')}
                    </div>
                </div>
            ` : '';

            const miniZonesHtml = (zones?.mini?.length > 0) ? `
                <div>
                    <strong>Mini zonas:</strong>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        ${zones.mini.map(zone => `<span class="badge bg-light text-dark">${zone}</span>`).join('')}
                    </div>
                </div>
            ` : '';

            let markAsCompletedForm = '';

            let shotsHtml = '';
            const shotsNumber = parseInt(shots, 10);

            // Condición 1: Si shots es un string vacío (''), no se muestra nada.
            // Esto se cumple por defecto al inicializar shotsHtml = ''.

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Condición 2: Si es un número entero igual a cero y shotsUrl no está vacío.
            if (shotsNumber === 0 && shotsUrl) {
                // Se necesita el token CSRF para que el formulario de Laravel funcione.
                shotsHtml = `
                <div>
                    <strong>Cantidad de disparos en cabina:</strong>
                    <form action="${shotsUrl}" method="POST" class="mt-2 needs-validation" novalidate>
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <div class="input-group">
                            <input type="number" id="initial-shots" class="form-control" placeholder="Disparos (inicial)" min="1" aria-label="Disparos (inicial)" required>
                            <input type="number" id="final-shots" class="form-control" placeholder="Disparos (final)" min="1" aria-label="Disparos (final)" required>
                            <input type="number" id="shots" readonly name="shots" required class="form-control" placeholder="Disparos (total)" min="1" aria-label="Disparos (total)" required>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                         <div class="invalid-feedback">
                            Por favor, ingrese un número de disparos mayor a cero.
                        </div>
                    </form>
                </div>
                `;
            }
            // Condición 3: Si es un número entero mayor a cero.
            else if (shotsNumber > 0) {
                shotsHtml = `<div><strong>Disparos en cabina:</strong> <span class="badge bg-success">${shots}</span></div>`;
            }

            if (markAsCompletedUrl ) {

                markAsCompletedForm = `
                <div>
                    <form action="${markAsCompletedUrl}" method="POST" class="mt-2 needs-validation" novalidate>
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="btn btn-primary">Marcar como completada</button>
                    </form>
                </div>
                `;
            }

            // Construir el cuerpo completo del modal
            modalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card bg-transparent">
                            <div class="card-body">
                                <h6 class="text-secondary small text-uppercase mb-3">Detalles de la Cita</h6>
                                <div class="vstack gap-2">
                                    <div><strong>Tratamiento:</strong> ${details.treatment}</div>
                                    <div><strong>Sesión:</strong> #${details.session_number}</div>
                                    <div><strong>Fecha:</strong> ${details.date}</div>
                                    <div><strong>Hora:</strong> ${details.time}</div>
                                    <div><strong>Estado:</strong> <span class="badge text-bg-info">${details.status}</span></div>
                                    ${bigZonesHtml}
                                    ${miniZonesHtml}
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
            const result = initial - final;

            // Mostramos el resultado (opcional: evitar negativos)
            totalInput.value = result > 0 ? result : 0;
        }
    }

});
