document.addEventListener('DOMContentLoaded', function () {

    // --- DOM ELEMENTS ---
    const quantityInputs = document.querySelectorAll('.item-amount');
    const zoneCheckboxes = document.querySelectorAll('.checkbox-zone');
    const purchaseSummary = document.getElementById('purchase-summary');
    const totalToPay = document.getElementById('total');
    const otherLargeZoneInput = document.getElementById('another-big-zone');
    const otherMiniZoneInput = document.getElementById('another-mini-zone');
    const purchaseSummaryContainer = document.getElementById('purchase-sumary-container');
    const paymentButtonContainer = document.getElementById('payment-button-container');

    // --- APPLICATION STATE ---
    let allowedLargeZones = 0;
    let allowedMiniZones = 0;

    // --- FUNCTIONS ---

    /**
     * Hides or shows the entire purchase summary section based on whether any package is selected.
     */
    function updateSummaryVisibility() {
        const hasSelectedItems = allowedLargeZones > 0 || allowedMiniZones > 0;
        purchaseSummaryContainer.style.display = hasSelectedItems ? 'block' : 'none';
    }

    /**
     * Hides or shows the payment button.
     * The button is only visible if the number of selected zones exactly matches the number of allowed zones.
     */
    function updatePaymentButtonVisibility() {
        const selectedLargeZones = document.querySelectorAll('.checkbox-zone[data-type="big"]:checked').length + (otherLargeZoneInput.value.trim() !== '' ? 1 : 0);
        const selectedMiniZones = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length + (otherMiniZoneInput.value.trim() !== '' ? 1 : 0);

        const allZonesSelected = selectedLargeZones === allowedLargeZones && selectedMiniZones === allowedMiniZones;
        const hasAllowedZones = allowedLargeZones > 0 || allowedMiniZones > 0;

        paymentButtonContainer.disabled = (allZonesSelected && hasAllowedZones) ? false : true;
    }

    /**
     * Calculates the total cost and the number of allowed zones based on selected packages.
     * Updates the purchase summary in the UI.
     */
    function calculateTotals() {
        let total = 0;
        let summaryHtml = '';
        allowedLargeZones = 0;
        allowedMiniZones = 0;

        // Calculate main packages
        quantityInputs.forEach(input => {
            if (input.dataset.type === 'package') {
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    const pkg = packages.find(p => p.id == input.dataset.id);
                    total += pkg.price * quantity;
                    allowedLargeZones += pkg.big_zones * quantity;
                    allowedMiniZones += pkg.mini_zones * quantity;
                    summaryHtml += `<tr><td>${quantity} x ${pkg.name} ($${(pkg.price).toLocaleString('es-CL')})</td><td class="text-end">$${(pkg.price * quantity).toLocaleString('es-CL')}</td></tr>`;
                }
            }
        });

        // Calculate additional zones
        quantityInputs.forEach(input => {
            if (input.dataset.type === 'additional') {
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    const additionalZone = additionalZones.find(p => p.id == input.dataset.id);
                    total += additionalZone.price * quantity;
                    if (additionalZone.id === 'big') {
                        allowedLargeZones += 1 * quantity;
                    } else {
                        allowedMiniZones += 1 * quantity;
                    }
                    summaryHtml += `<tr><td>${quantity} x ${additionalZone.name} ($${(additionalZone.price).toLocaleString('es-CL')})</td><td class="text-end">$${(additionalZone.price * quantity).toLocaleString('es-CL')}</td></tr>`;
                }
            }
        });

        purchaseSummary.innerHTML = summaryHtml;
        totalToPay.innerText = `$${total.toLocaleString('es-CL')}`;
        updateSummaryVisibility();
        validateZoneCheckboxes();
    }

    /**
     * Validates and disables/enables zone checkboxes based on the number of allowed selections.
     */
    function validateZoneCheckboxes() {
        let selectedLargeZones = document.querySelectorAll('.checkbox-zone[data-type="big"]:checked').length;
        if (otherLargeZoneInput.value.trim() !== '') {
            selectedLargeZones++;
        }

        let selectedMiniZones = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length;
        if (otherMiniZoneInput.value.trim() !== '') {
            selectedMiniZones++;
        }

        // Enable/disable large zone checkboxes
        document.querySelectorAll('.checkbox-zone[data-type="big"]').forEach(checkbox => {
            if (!checkbox.checked && selectedLargeZones >= allowedLargeZones) {
                checkbox.disabled = true;
            } else {
                checkbox.disabled = false;
            }
        });
        otherLargeZoneInput.disabled = (selectedLargeZones >= allowedLargeZones && otherLargeZoneInput.value.trim() === '');

        // Enable/disable mini zone checkboxes
        document.querySelectorAll('.checkbox-zone[data-type="mini"]').forEach(checkbox => {
            if (!checkbox.checked && selectedMiniZones >= allowedMiniZones) {
                checkbox.disabled = true;
            } else {
                checkbox.disabled = false;
            }
        });
        otherMiniZoneInput.disabled = (selectedMiniZones >= allowedMiniZones && otherMiniZoneInput.value.trim() === '');

        updatePaymentButtonVisibility();
    }

    // --- EVENT LISTENERS ---
    quantityInputs.forEach(input => {
        input.addEventListener('change', calculateTotals);
    });

    zoneCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateZoneCheckboxes);
    });

    otherLargeZoneInput.addEventListener('input', validateZoneCheckboxes);
    otherMiniZoneInput.addEventListener('input', validateZoneCheckboxes);

    // Initialize calculations on page load
    calculateTotals();
});

document.addEventListener('DOMContentLoaded', function () {
    $(".show-terms-conditions-modal").on('click',function() {
        if ($(this).is(':checked')) {
            const modal = new bootstrap.Modal('#termsConditionsModal');
            modal.show();
        }
    });
    $("#acceptTermsConditions").on('click',function() {
        $("#nextInformedConsent").removeAttr('disabled');
        bootstrap.Modal.getInstance('#termsConditionsModal').hide();
    });
}, false);

document.addEventListener('DOMContentLoaded', function () {

    // Función para el Checkbox
    const checkboxes = document.querySelectorAll('.trigger-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);

            if (this.checked) {
                // Si se marca y estaba en 0, poner 1
                if (parseInt(input.value) === 0 || input.value === '') {
                    input.value = 1;
                }
            } else {
                // Si se desmarca, volver a 0
                input.value = 0;
            }
        });
    });

    // Función para el Input Numérico
    const inputs = document.querySelectorAll('.item-amount');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Buscamos el checkbox asociado usando el ID inverso o navegando el DOM
            // Aquí asumimos la estructura de IDs usada arriba
            const type = this.getAttribute('data-type');
            const id = this.getAttribute('data-id');
            const checkbox = document.getElementById(`check-${type}-${id}`);

            if (parseInt(this.value) > 0) {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
    });
});
