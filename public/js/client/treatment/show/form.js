document.addEventListener('DOMContentLoaded', function () {

    // --- DOM ELEMENTS ---
    const quantityInputs = document.querySelectorAll('.item-amount');
    const zoneCheckboxes = document.querySelectorAll('.checkbox-zone');
    const purchaseSummary = document.getElementById('purchase-summary');
    const totalToPay = document.getElementById('total');
    const otherLargeZoneInput = document.getElementById('another-big-zone');
    const otherMiniZoneInput = document.getElementById('another-mini-zone');
    const purchaseSummaryContainer = document.getElementById('purchase-sumary-container');

    // Elementos del Modal
    const btnOpenPaymentModal = document.getElementById('btn-open-payment-modal');
    const paymentModalElement = document.getElementById('paymentSelectionModal');
    let paymentModal;
    if(paymentModalElement) {
        paymentModal = new bootstrap.Modal(paymentModalElement);
    }
    const modalTotalAmount = document.getElementById('modal-total-amount');
    const modalInstallmentAmount = document.getElementById('modal-installment-amount');
    const btnPayInstallment = document.getElementById('btn-pay-installment');

    // --- STATE ---
    let allowedLargeZones = 0;
    let allowedMiniZones = 0;

    // Totales Globales
    let currentTotal = 0;
    let currentInitialPayment = 0;
    let showInstallmentOption = false; // Variable única de control

    // --- FUNCTIONS ---

    function updateSummaryVisibility() {
        const hasSelectedItems = currentTotal > 0;
        if(purchaseSummaryContainer) {
            purchaseSummaryContainer.style.display = hasSelectedItems ? 'block' : 'none';
        }
    }

    function updatePaymentButtonVisibility() {
        // Contar zonas seleccionadas
        let selectedLarge = document.querySelectorAll('.checkbox-zone[data-type="big"]:checked').length;
        if (otherLargeZoneInput && otherLargeZoneInput.value.trim() !== '') selectedLarge++;

        let selectedMini = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length;
        if (otherMiniZoneInput && otherMiniZoneInput.value.trim() !== '') selectedMini++;

        // Validar selección completa
        const allZonesSelected = (selectedLarge === allowedLargeZones) && (selectedMini === allowedMiniZones);
        const hasPurchasedSomething = currentTotal > 0;

        if(btnOpenPaymentModal) {
            btnOpenPaymentModal.disabled = !(allZonesSelected && hasPurchasedSomething);
        }
    }

    function calculateTotals() {
        let total = 0;
        let installmentAccumulator = 0; // Acumulador para la "1ra Cuota"
        let atLeastOnePkgHasInstallments = false;
        let summaryHtml = '';

        allowedLargeZones = 0;
        allowedMiniZones = 0;

        // 1. Procesar Paquetes (Packages)
        quantityInputs.forEach(input => {
            if (input.dataset.type === 'package') {
                const quantity = parseInt(input.value) || 0;

                if (quantity > 0) {
                    // Buscar paquete en el JSON inyectado desde Blade
                    const pkg = packages.find(p => p.id == input.dataset.id);

                    if(pkg) {
                        const price = parseFloat(pkg.price);
                        const lineTotal = price * quantity;

                        // Totales generales
                        total += lineTotal;
                        allowedLargeZones += parseInt(pkg.big_zones) * quantity;
                        allowedMiniZones += parseInt(pkg.mini_zones) * quantity;

                        // --- LÓGICA CRÍTICA DE CUOTAS ---
                        // Verificamos explícitamente si permite cuotas (convirtiendo a int para asegurar)
                        const allowsInstallments = parseInt(pkg.allow_installments) === 1;
                        const hasInstallmentData = pkg.installments && pkg.installments.length > 0;

                        if (allowsInstallments && hasInstallmentData) {
                            atLeastOnePkgHasInstallments = true;
                            // Si tiene cuotas, sumamos solo el valor de la 1ra cuota
                            const firstInstPrice = parseFloat(pkg.installments[0].price);
                            installmentAccumulator += firstInstPrice * quantity;
                        } else {
                            // Si NO tiene cuotas, se suma el PRECIO TOTAL al "pago inicial"
                            installmentAccumulator += lineTotal;
                        }

                        summaryHtml += `
                            <tr>
                                <td>${quantity} x ${pkg.name}</td>
                                <td class="text-end">$${lineTotal.toLocaleString('es-CL')}</td>
                            </tr>`;
                    }
                }
            }
        });

        // 2. Procesar Adicionales
        quantityInputs.forEach(input => {
            if (input.dataset.type === 'additional') {
                const quantity = parseInt(input.value) || 0;

                if (quantity > 0) {
                    const addData = additionalZones.find(p => p.id == input.dataset.id);
                    if(addData) {
                        const price = parseFloat(addData.price);
                        const lineTotal = price * quantity;

                        total += lineTotal;
                        // Los adicionales SIEMPRE se pagan completos al inicio
                        installmentAccumulator += lineTotal;

                        if (addData.id === 'big') {
                            allowedLargeZones += 1 * quantity;
                        } else {
                            allowedMiniZones += 1 * quantity;
                        }

                        summaryHtml += `
                            <tr>
                                <td>${quantity} x ${addData.name}</td>
                                <td class="text-end">$${lineTotal.toLocaleString('es-CL')}</td>
                            </tr>`;
                    }
                }
            }
        });

        // Actualizar Variables Globales
        currentTotal = total;
        currentInitialPayment = installmentAccumulator;

        // Decidir si mostrar el botón de cuotas:
        // 1. Debe haber un paquete con cuotas.
        // 2. El pago inicial debe ser MENOR al total (si es igual, es tontería mostrarlo).
        // 3. El pago inicial debe ser MAYOR a 0.
        showInstallmentOption = atLeastOnePkgHasInstallments && (currentInitialPayment < currentTotal) && (currentInitialPayment > 0);

        // Renderizar UI
        if(purchaseSummary) purchaseSummary.innerHTML = summaryHtml;
        if(totalToPay) totalToPay.innerText = `$${total.toLocaleString('es-CL')}`;

        updateSummaryVisibility();
        validateZoneCheckboxes();
    }

    function validateZoneCheckboxes() {
        // Lógica de validación de checkboxes (sin cambios funcionales)
        let selectedLarge = document.querySelectorAll('.checkbox-zone[data-type="big"]:checked').length;
        if (otherLargeZoneInput && otherLargeZoneInput.value.trim() !== '') selectedLarge++;

        let selectedMini = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length;
        if (otherMiniZoneInput && otherMiniZoneInput.value.trim() !== '') selectedMini++;

        document.querySelectorAll('.checkbox-zone[data-type="big"]').forEach(cb => {
            cb.disabled = !cb.checked && selectedLarge >= allowedLargeZones;
        });
        if(otherLargeZoneInput) {
            otherLargeZoneInput.disabled = (selectedLarge >= allowedLargeZones && otherLargeZoneInput.value.trim() === '');
        }

        document.querySelectorAll('.checkbox-zone[data-type="mini"]').forEach(cb => {
            cb.disabled = !cb.checked && selectedMini >= allowedMiniZones;
        });
        if(otherMiniZoneInput) {
            otherMiniZoneInput.disabled = (selectedMini >= allowedMiniZones && otherMiniZoneInput.value.trim() === '');
        }

        updatePaymentButtonVisibility();
    }

    // --- EVENT LISTENERS ---

    quantityInputs.forEach(input => {
        input.addEventListener('change', calculateTotals);
        input.addEventListener('input', calculateTotals);
    });

    zoneCheckboxes.forEach(cb => {
        cb.addEventListener('change', validateZoneCheckboxes);
    });

    if(otherLargeZoneInput) otherLargeZoneInput.addEventListener('input', validateZoneCheckboxes);
    if(otherMiniZoneInput) otherMiniZoneInput.addEventListener('input', validateZoneCheckboxes);

    // LOGICA DEL MODAL
    if(btnOpenPaymentModal) {
        btnOpenPaymentModal.addEventListener('click', function() {
            // 1. Mostrar Precio Total
            if(modalTotalAmount) modalTotalAmount.innerText = `$${currentTotal.toLocaleString('es-CL')}`;

            // 2. Configurar Botón de Cuotas
            if(btnPayInstallment && modalInstallmentAmount) {
                if (showInstallmentOption) {
                    // Mostrar botón con el precio calculado
                    modalInstallmentAmount.innerText = `$${currentInitialPayment.toLocaleString('es-CL')}`;
                    btnPayInstallment.style.setProperty('display', 'flex', 'important'); // Forzar display flex
                } else {
                    // Ocultar botón
                    btnPayInstallment.style.setProperty('display', 'none', 'important');
                }
            }

            if(paymentModal) paymentModal.show();
        });
    }

    // Inicializar
    calculateTotals();
});

// Helpers de UI (sin cambios)
document.addEventListener('DOMContentLoaded', function () {
    $(".show-terms-conditions-modal").on('click', function() {
        if ($(this).is(':checked')) new bootstrap.Modal('#termsConditionsModal').show();
    });
    $("#acceptTermsConditions").on('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('termsConditionsModal')).hide();
        document.getElementById('termsConditions').checked = true;
    });
    document.querySelectorAll('.trigger-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const input = document.getElementById(this.getAttribute('data-target'));
            if(input) {
                input.value = this.checked ? (input.value == 0 ? 1 : input.value) : 0;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
    document.querySelectorAll('.item-amount').forEach(input => {
        input.addEventListener('input', function() {
            const cb = document.getElementById(`check-${this.getAttribute('data-type')}-${this.getAttribute('data-id')}`);
            if(cb) cb.checked = parseInt(this.value) > 0;
        });
    });
});
