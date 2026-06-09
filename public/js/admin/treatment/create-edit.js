document.addEventListener('DOMContentLoaded', function() {
    const mainImageInput = document.getElementById('main_image');
    const imagePreview = document.getElementById('imagePreview');
    const sessionsInput = document.getElementById('sessions'); // Input global de sesiones

    // --- Manejo de Imagen ---
    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }

    // --- Delegación de Eventos ---
    document.body.addEventListener('click', function(event) {
        const target = event.target;

        // 1. Agregar Paquete
        if (target.classList.contains('add-package-btn')) {
            const branchId = target.dataset.branchId;
            const container = document.getElementById(`packages-container-${branchId}`);
            const newIndex = Date.now();
            const packageFormHtml = getPackageFormTemplate(branchId, newIndex);
            container.insertAdjacentHTML('beforeend', packageFormHtml);
        }

        // 2. Eliminar Paquete
        if (target.closest('.remove-package-btn')) {
            target.closest('.package-form-row').remove();
        }

        // 3. Agregar Cuota
        if (target.closest('.add-installment-btn')) {
            const btn = target.closest('.add-installment-btn');
            const branchId = btn.dataset.branch;
            const packageKey = btn.dataset.package;
            const container = document.getElementById(`installments-container-${branchId}-${packageKey}`);

            // Validación: No exceder sesiones
            const row = btn.closest('.package-form-row');
            const customSessionsToggle = row.querySelector('.toggle-custom-sessions');
            const packageSessionsInput = row.querySelector('.package-sessions-input');
            
            let maxSessions = parseInt(sessionsInput.value) || 0;
            if (customSessionsToggle && customSessionsToggle.checked) {
                const customVal = parseInt(packageSessionsInput.value) || 0;
                if (customVal > 0) {
                    maxSessions = customVal;
                } else {
                    alert('Por favor ingrese primero la cantidad de sesiones personalizadas del paquete.');
                    packageSessionsInput.focus();
                    return;
                }
            } else if (maxSessions === 0) {
                alert('Por favor ingrese primero la cantidad de sesiones del tratamiento.');
                sessionsInput.focus();
                return;
            }

            const currentInstallments = container.querySelectorAll('.installment-row').length;

            if (currentInstallments >= maxSessions) {
                alert(`No puedes agregar más de ${maxSessions} cuotas (límite de sesiones).`);
                return;
            }

            const installmentIndex = Date.now() + Math.floor(Math.random() * 1000);
            const nextNumber = currentInstallments + 1;

            const html = `
                <div class="row g-2 mb-2 installment-row">
                    <div class="col-auto d-flex align-items-center">
                        <span class="badge bg-secondary installment-label">Cuota ${nextNumber}</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Precio $</span>
                            <input type="text" inputmode="numeric"
                                   name="branches[${branchId}][packages][${packageKey}][installments][${installmentIndex}][price]"
                                   class="form-control currency-input" required>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-installment-btn"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        // 4. Eliminar Cuota
        if (target.closest('.remove-installment-btn')) {
            const row = target.closest('.installment-row');
            const container = row.parentElement;
            row.remove();
            renumberInstallments(container);
        }
    });

    // --- Manejo de Switches (Change event) ---
    document.body.addEventListener('change', function(event) {
        if (event.target.classList.contains('toggle-installments')) {
            const wrapper = event.target.closest('.package-form-row').querySelector('.installments-wrapper');
            if (event.target.checked) {
                wrapper.classList.remove('d-none');
            } else {
                wrapper.classList.add('d-none');
            }
        }

        if (event.target.classList.contains('toggle-custom-sessions')) {
            const row = event.target.closest('.package-form-row');
            const wrapper = row.querySelector('.custom-sessions-wrapper');
            const input = row.querySelector('.package-sessions-input');
            if (event.target.checked) {
                wrapper.classList.remove('d-none');
                input.setAttribute('required', 'required');
            } else {
                wrapper.classList.add('d-none');
                input.removeAttribute('required');
                input.value = '';
            }
        }
    });

    // --- Funciones Auxiliares ---

    function renumberInstallments(container) {
        const rows = container.querySelectorAll('.installment-row');
        rows.forEach((row, index) => {
            const badge = row.querySelector('.installment-label');
            if (badge) badge.textContent = `Cuota ${index + 1}`;
        });
    }

    function getPackageFormTemplate(branchId, index) {
        // Template actualizado con la estructura de cuotas
        return `
            <div class="row g-3 p-3 border rounded mb-3 package-form-row" data-package-id="${index}">
                <div class="col-12 col-md-3">
                    <label class="form-label">Nombre del paquete</label>
                    <input type="text" name="branches[${branchId}][packages][${index}][name]" class="form-control" placeholder="Ej: Paquete Premium" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Precio Total</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" inputmode="numeric" name="branches[${branchId}][packages][${index}][price]" class="form-control currency-input" placeholder="0" required>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Zonas Grandes</label>
                    <input type="number" name="branches[${branchId}][packages][${index}][big_zones]" class="form-control" step="1" min="0" required>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Mini Zonas</label>
                    <input type="number" name="branches[${branchId}][packages][${index}][mini_zones]" class="form-control" step="1" min="0" required>
                </div>

                <div class="col-12 mt-3">
                    <div class="d-flex flex-wrap gap-4 align-items-center mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-installments" type="checkbox" role="switch"
                                id="allow_installments_${branchId}_${index}"
                                name="branches[${branchId}][packages][${index}][allow_installments]" value="1">
                            <label class="form-check-label" for="allow_installments_${branchId}_${index}">Habilitar pago en cuotas</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-custom-sessions" type="checkbox" role="switch"
                                id="custom_sessions_${branchId}_${index}"
                                name="branches[${branchId}][packages][${index}][custom_sessions]" value="1">
                            <label class="form-check-label" for="custom_sessions_${branchId}_${index}">Personalizar número de sesiones</label>
                        </div>
                        <div class="custom-sessions-wrapper d-none">
                            <div class="d-flex align-items-center">
                                <label class="me-2 mb-0 fw-semibold">Nº de Sesiones:</label>
                                <input type="number" name="branches[${branchId}][packages][${index}][sessions]" class="form-control form-control-sm package-sessions-input"
                                    value="" style="width: 80px;" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="installments-wrapper mt-3 ps-3 border-start border-3 border-info d-none">
                        <h6>Configuración de Cuotas</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Condiciones de cuotas</label>
                            <input type="text" name="branches[${branchId}][packages][${index}][installment_conditions]" class="form-control"
                                value="Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión" placeholder="Ej: Cancela el 50% del tratamiento para comenzar..." required>
                        </div>
                        <div class="installments-container" id="installments-container-${branchId}-${index}">
                            <!-- Las cuotas se agregarán aquí -->
                        </div>
                        <button type="button" class="btn btn-sm btn-info text-white mt-2 add-installment-btn"
                                data-branch="${branchId}" data-package="${index}">
                            <i class="bi bi-plus"></i> Agregar Cuota
                        </button>
                    </div>
                </div>

                <div class="col-12 mt-3 text-end border-top pt-2">
                    <button type="button" class="btn btn-danger btn-sm remove-package-btn">
                        <i class="bi bi-trash-fill"></i> Quitar Paquete Completo
                    </button>
                </div>
            </div>
        `;
    }
});
