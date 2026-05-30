document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. REFERENCIAS AL DOM
    // ==========================================

    // Formulario Principal
    const quantityInputs = document.querySelectorAll('.item-amount');
    const zoneCheckboxes = document.querySelectorAll('.checkbox-zone');
    const totalToPayDisplay = document.getElementById('total');
    const purchaseSummary = document.getElementById('purchase-summary');
    const purchaseSummaryContainer = document.getElementById('purchase-sumary-container');

    // Inputs de "Otra Zona"
    const otherLargeZoneInput = document.getElementById('another-big-zone');
    const otherMiniZoneInput = document.getElementById('another-mini-zone');

    // Botón Principal
    const btnOpenPaymentModal = document.getElementById('btn-open-payment-modal');

    // Elementos del Modal de Pago
    const paymentModalElement = document.getElementById('paymentSelectionModal');
    let paymentModal = paymentModalElement ? new bootstrap.Modal(paymentModalElement) : null;

    // Elementos internos del Modal
    const modalTotalAmount = document.getElementById('modal-total-amount');
    const modalInstallmentAmount = document.getElementById('modal-installment-amount');
    const optionInstallmentContainer = document.getElementById('option-installment-container');
    const installmentConditionsText = document.getElementById('installment-conditions-text');

    // Radios de Modalidad (Cuota vs Total)
    const radioTypeFull = document.getElementById('pt_full');
    const radioTypeInstallment = document.getElementById('pt_installment');
    const radioInputsType = document.querySelectorAll('input[name="payment_type"]');

    // Radios de Método (Wompi vs Transfer vs Cash)
    const radioInputsMethod = document.querySelectorAll('input[name="payment_method"]');

    // ==========================================
    // 2. ESTADO DE LA APLICACIÓN
    // ==========================================
    let allowedLargeZones = 0;
    let allowedMiniZones = 0;

    let currentTotal = 0;
    let currentInitialPayment = 0; // Lo que se paga si elige "1ra Cuota"
    let showInstallmentOption = false; // ¿Debe mostrarse la opción de cuotas?

    // ==========================================
    // 3. LÓGICA DE CÁLCULO (CORE)
    // ==========================================

    function calculateTotals() {
        let total = 0;
        let installmentAccumulator = 0;
        let atLeastOnePkgHasInstallments = false;
        let summaryHtml = '';
        let selectedInstallmentConditions = [];

        allowedLargeZones = 0;
        allowedMiniZones = 0;

        // --- A. Procesar Paquetes ---
        quantityInputs.forEach(input => {
            if (input.dataset.type === 'package') {
                const quantity = parseInt(input.value) || 0;

                if (quantity > 0) {
                    // 'packages' viene definido desde el Blade en un script tag
                    const pkg = packages.find(p => p.id == input.dataset.id);

                    if (pkg) {
                        const price = parseFloat(pkg.price);
                        const lineTotal = price * quantity;

                        // Sumar a totales generales
                        total += lineTotal;
                        allowedLargeZones += parseInt(pkg.big_zones) * quantity;
                        allowedMiniZones += parseInt(pkg.mini_zones) * quantity;

                        // Lógica de Cuotas Mixtas:
                        // Verificamos si el paquete permite cuotas y tiene datos de cuotas
                        const allowsInstallments = pkg.allow_installments === true || parseInt(pkg.allow_installments) === 1;
                        const hasInstallmentData = pkg.installments && pkg.installments.length > 0;

                        if (allowsInstallments && hasInstallmentData) {
                            atLeastOnePkgHasInstallments = true;
                            // Sumamos solo el valor de la 1ra cuota
                            const firstInstPrice = parseFloat(pkg.installments[0].price);
                            installmentAccumulator += firstInstPrice * quantity;

                            const cond = pkg.installment_conditions || 'Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión';
                            if (!selectedInstallmentConditions.includes(cond)) {
                                selectedInstallmentConditions.push(cond);
                            }
                        } else {
                            // Si NO tiene cuotas, se suma el PRECIO TOTAL al acumulador inicial
                            installmentAccumulator += lineTotal;
                        }

                        summaryHtml += `
                            <tr>
                                <td>${quantity} x ${pkg.name}</td>
                                <td class="text-end">$${lineTotal.toLocaleString('es-CO')}</td>
                            </tr>`;
                    }
                }
            }
        });

        // --- B. Procesar Zonas Adicionales ---
        quantityInputs.forEach(input => {
            if (input.dataset.type === 'additional') {
                const quantity = parseInt(input.value) || 0;

                if (quantity > 0) {
                    // 'additionalZones' viene definido desde el Blade
                    const addData = additionalZones.find(p => p.id == input.dataset.id);

                    if (addData) {
                        const price = parseFloat(addData.price);
                        const lineTotal = price * quantity;

                        total += lineTotal;
                        // Adicionales SIEMPRE se pagan completos al inicio
                        installmentAccumulator += lineTotal;

                        if (addData.id === 'big') {
                            allowedLargeZones += 1 * quantity;
                        } else {
                            allowedMiniZones += 1 * quantity;
                        }

                        summaryHtml += `
                            <tr>
                                <td>${quantity} x ${addData.name}</td>
                                <td class="text-end">$${lineTotal.toLocaleString('es-CO')}</td>
                            </tr>`;
                    }
                }
            }
        });

        // --- C. Actualizar Estado Global ---
        currentTotal = total;
        currentInitialPayment = installmentAccumulator;

        // Mostrar opción de cuotas solo si:
        // 1. Hay al menos un paquete con cuotas.
        // 2. El pago inicial es menor al total (si es igual, no tiene sentido la opción).
        // 3. El pago inicial es mayor a 0.
        showInstallmentOption = atLeastOnePkgHasInstallments && (currentInitialPayment < currentTotal) && (currentInitialPayment > 0);

        // --- D. Actualizar UI Resumen ---
        if (purchaseSummary) purchaseSummary.innerHTML = summaryHtml;
        if (totalToPayDisplay) totalToPayDisplay.innerText = `$${total.toLocaleString('es-CO')}`;

        if (installmentConditionsText) {
            if (selectedInstallmentConditions.length > 0) {
                installmentConditionsText.innerText = selectedInstallmentConditions.join(' / ');
            } else {
                installmentConditionsText.innerText = 'Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión';
            }
        }

        if (purchaseSummaryContainer) {
            purchaseSummaryContainer.style.display = total > 0 ? 'block' : 'none';
        }

        // --- E. Re-validar UI ---
        validateZoneCheckboxes();
    }

    // ==========================================
    // 4. VALIDACIÓN DE ZONAS (CHECKBOXES)
    // ==========================================

    function validateZoneCheckboxes() {
        // Contar seleccionados actuales
        let selectedLarge = document.querySelectorAll('.checkbox-zone[data-type="big"]:checked').length;
        if (otherLargeZoneInput && otherLargeZoneInput.value.trim() !== '') selectedLarge++;

        let selectedMini = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length;
        if (otherMiniZoneInput && otherMiniZoneInput.value.trim() !== '') selectedMini++;

        // Habilitar/Deshabilitar Checkboxes Grandes
        document.querySelectorAll('.checkbox-zone[data-type="big"]').forEach(cb => {
            if (!cb.checked && selectedLarge >= allowedLargeZones) {
                cb.disabled = true;
            } else {
                cb.disabled = false;
            }
        });
        if (otherLargeZoneInput) {
            otherLargeZoneInput.disabled = (selectedLarge >= allowedLargeZones && otherLargeZoneInput.value.trim() === '');
        }

        // Habilitar/Deshabilitar Checkboxes Mini
        document.querySelectorAll('.checkbox-zone[data-type="mini"]').forEach(cb => {
            if (!cb.checked && selectedMini >= allowedMiniZones) {
                cb.disabled = true;
            } else {
                cb.disabled = false;
            }
        });
        if (otherMiniZoneInput) {
            otherMiniZoneInput.disabled = (selectedMini >= allowedMiniZones && otherMiniZoneInput.value.trim() === '');
        }

        updatePaymentButtonVisibility();
    }

    function updatePaymentButtonVisibility() {
        // Validar que se hayan seleccionado exactamente las zonas permitidas
        // (Contando inputs manuales)
        let selectedLarge = document.querySelectorAll('.checkbox-zone[data-type="big"]:checked').length;
        if (otherLargeZoneInput && otherLargeZoneInput.value.trim() !== '') selectedLarge++;

        let selectedMini = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length;
        if (otherMiniZoneInput && otherMiniZoneInput.value.trim() !== '') selectedMini++;

        const allZonesSelected = (selectedLarge === allowedLargeZones) && (selectedMini === allowedMiniZones);
        const hasPurchasedSomething = currentTotal > 0;

        // Habilitar botón de "Continuar" solo si todo cuadra
        if (btnOpenPaymentModal) {
            btnOpenPaymentModal.disabled = !(allZonesSelected && hasPurchasedSomething);
        }
    }

    // ==========================================
    // 5. GESTIÓN DEL MODAL DE PAGO
    // ==========================================

    // A. Abrir Modal y Configurar Valores
    if (btnOpenPaymentModal) {
        btnOpenPaymentModal.addEventListener('click', function () {
            // Actualizar montos visuales
            if (modalTotalAmount) {
                modalTotalAmount.innerText = `$${currentTotal.toLocaleString('es-CO')}`;
            }

            if (showInstallmentOption) {
                // Configurar opción de cuota
                if (modalInstallmentAmount) {
                    modalInstallmentAmount.innerText = `$${currentInitialPayment.toLocaleString('es-CO')}`;
                }
                if (optionInstallmentContainer) {
                    optionInstallmentContainer.style.display = 'block'; // Mostrar tarjeta
                    optionInstallmentContainer.classList.remove('d-none');
                }
            } else {
                // Ocultar opción de cuota y forzar selección de Total
                if (optionInstallmentContainer) {
                    optionInstallmentContainer.style.display = 'none';
                    optionInstallmentContainer.classList.add('d-none');
                }
                if (radioTypeFull) radioTypeFull.checked = true;
            }

            // Actualizar estilos visuales (borde azul)
            updateCardStyles();

            // Mostrar modal
            if (paymentModal) paymentModal.show();
        });
    }

    const radioTypeAbono = document.getElementById('pt_abono');
    const abonoInputContainer = document.getElementById('abono-input-container');
    const abonoAmountInput = document.getElementById('abono_amount');
    const abonoLimitsHint = document.getElementById('abono-limits-hint');

    // B. Estilos visuales de tarjetas (Borde Azul al seleccionar)
    function updateCardStyles() {
        document.querySelectorAll('.payment-option-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.classList.add('border-primary', 'shadow-sm');
            } else {
                card.classList.remove('border-primary', 'shadow-sm');
            }
        });

        // Abono sub-fields display
        const abonoSelected = radioTypeAbono && radioTypeAbono.checked;
        if (abonoInputContainer) {
            abonoInputContainer.style.display = abonoSelected ? 'block' : 'none';
        }
        if (abonoSelected) {
            if (abonoAmountInput) {
                abonoAmountInput.required = true;
                if (!abonoAmountInput.value) {
                    abonoAmountInput.value = Math.min(minimumAbonoAmount, currentTotal);
                }
                abonoAmountInput.max = currentTotal;
                abonoAmountInput.min = Math.min(minimumAbonoAmount, currentTotal);
                if (abonoLimitsHint) {
                    abonoLimitsHint.innerText = `Mínimo: $${parseFloat(abonoAmountInput.min).toLocaleString('es-CO')} | Máximo: $${parseFloat(abonoAmountInput.max).toLocaleString('es-CO')}`;
                }
            }
        } else {
            if (abonoAmountInput) {
                abonoAmountInput.required = false;
            }
        }
    }

    radioInputsType.forEach(radio => {
        radio.addEventListener('change', updateCardStyles);
    });

    const purchaseForm = document.getElementById('purchaseForm');
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', function(e) {
            if (radioTypeAbono && radioTypeAbono.checked) {
                const amount = parseInt(abonoAmountInput.value) || 0;
                const min = parseInt(abonoAmountInput.min) || minimumAbonoAmount;
                const max = parseInt(abonoAmountInput.max) || currentTotal;
                if (amount < min || amount > max) {
                    e.preventDefault();
                    alert(`El monto del abono debe estar entre $${min.toLocaleString('es-CO')} y $${max.toLocaleString('es-CO')}`);
                }
            }
        });
    }

    // C. Mostrar/Ocultar Detalles de Método de Pago
    function toggleMethodDetails() {
        // Ocultar todos los detalles primero
        document.querySelectorAll('.method-info').forEach(div => div.classList.add('d-none'));

        // Buscar cuál está seleccionado
        const selected = document.querySelector('input[name="payment_method"]:checked');
        if (selected) {
            const val = selected.value;
            if (val === 'WOMPI') {
                const el = document.getElementById('info-wompi');
                if (el) el.classList.remove('d-none');
            }
            if (val === 'TRANSFER') {
                const el = document.getElementById('info-transfer');
                if (el) el.classList.remove('d-none');
            }
            if (val === 'CASH') {
                const el = document.getElementById('info-cash');
                if (el) el.classList.remove('d-none');
            }
        }
    }

    radioInputsMethod.forEach(radio => {
        radio.addEventListener('change', toggleMethodDetails);
    });

    // ==========================================
    // 6. EVENT LISTENERS PRINCIPALES
    // ==========================================

    // Inputs de Cantidad
    quantityInputs.forEach(input => {
        input.addEventListener('change', calculateTotals);
        input.addEventListener('input', calculateTotals);

        // Sincronizar con checkbox de "item-amount" si existe (logica legacy visual)
        input.addEventListener('input', function() {
            const type = this.getAttribute('data-type');
            const id = this.getAttribute('data-id');
            const checkbox = document.getElementById(`check-${type}-${id}`);
            if(checkbox) checkbox.checked = parseInt(this.value) > 0;
        });
    });

    // Checkboxes de Zonas
    zoneCheckboxes.forEach(cb => {
        cb.addEventListener('change', validateZoneCheckboxes);
    });

    // Inputs de Texto para Zonas
    if (otherLargeZoneInput) otherLargeZoneInput.addEventListener('input', validateZoneCheckboxes);
    if (otherMiniZoneInput) otherMiniZoneInput.addEventListener('input', validateZoneCheckboxes);


    // ==========================================
    // 7. INICIALIZACIÓN
    // ==========================================
    calculateTotals();
    toggleMethodDetails();
    updateCardStyles();

});

// ==========================================
// 8. HELPERS (Términos y condiciones, etc.)
// ==========================================
document.addEventListener('DOMContentLoaded', function () {

    // Modal de Términos
    const termsCheck = document.querySelector(".show-terms-conditions-modal");
    if (termsCheck) {
        termsCheck.addEventListener('click', function(e) {
            if (this.checked) {
                // Prevenir check inmediato, mostrar modal primero
                e.preventDefault();
                const modalEl = document.getElementById('termsConditionsModal');
                if (modalEl) new bootstrap.Modal(modalEl).show();
            }
        });
    }

    const btnAcceptTerms = document.getElementById("acceptTermsConditions");
    if (btnAcceptTerms) {
        btnAcceptTerms.addEventListener('click', function() {
            const modalEl = document.getElementById('termsConditionsModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Marcar el checkbox ahora sí
            if (termsCheck) termsCheck.checked = true;
        });
    }

    // Checkboxes auxiliares (trigger-checkbox)
    document.querySelectorAll('.trigger-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if(input) {
                input.value = this.checked ? (input.value == 0 ? 1 : input.value) : 0;
                input.dispatchEvent(new Event('input')); // Disparar input para recalcular
            }
        });
    });
});
