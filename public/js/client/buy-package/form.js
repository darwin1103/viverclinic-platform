document.addEventListener('DOMContentLoaded', function () {

    // --- DOM ELEMENTS ---
    const quantityInputs = document.querySelectorAll('.item-amount');
    const zoneCheckboxes = document.querySelectorAll('.checkbox-zone');
    const purchaseSummary = document.getElementById('purchase-summary');
    const totalToPay = document.getElementById('total');
    const otherLargeZoneInput = document.getElementById('another-big-zone');
    const otherMiniZoneInput = document.getElementById('another-mini-zone');

    // --- APPLICATION STATE ---
    let allowedLargeZones = 0;
    let allowedMiniZones = 0;

    // --- FUNCTIONS ---

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
                    summaryHtml += `<tr><td>${quantity} x ${pkg.name}</td><td class="text-end">$${(pkg.price * quantity).toLocaleString('es-CL')}</td></tr>`;
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
                    if (additionalZone.id === 'grande') {
                        allowedLargeZones += 10 * quantity;
                    } else {
                        allowedMiniZones += 10 * quantity;
                    }
                    summaryHtml += `<tr><td>${quantity} x ${additionalZone.name}</td><td class="text-end">$${(additionalZone.price * quantity).toLocaleString('es-CL')}</td></tr>`;
                }
            }
        });

        purchaseSummary.innerHTML = summaryHtml;
        totalToPay.innerText = `$${total.toLocaleString('es-CL')}`;

        validateZoneCheckboxes();
    }

    /**
     * Validates and disables/enables zone checkboxes based on the number of allowed selections.
     */
    function validateZoneCheckboxes() {
        let selectedLargeZones = document.querySelectorAll('.checkbox-zone[data-type="grande"]:checked').length;
        if (otherLargeZoneInput.value.trim() !== '') {
            selectedLargeZones++;
        }

        let selectedMiniZones = document.querySelectorAll('.checkbox-zone[data-type="mini"]:checked').length;
        if (otherMiniZoneInput.value.trim() !== '') {
            selectedMiniZones++;
        }

        // Enable/disable large zone checkboxes
        document.querySelectorAll('.checkbox-zone[data-type="grande"]').forEach(checkbox => {
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
