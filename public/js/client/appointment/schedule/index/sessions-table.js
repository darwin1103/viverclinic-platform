// Sessions table module for handling session interactions
const SessionsTableModule = (function() {
    const elements = {
        tableBody: null,
        modalAgendar: null,
        modalRate: null
    };

    let sessions = [];

    function init() {
        cacheElements();
        loadSessionsData();
        attachEventListeners();
    }

    function cacheElements() {
        elements.tableBody = document.getElementById('sessionTableBody');
        elements.modalAgendar = document.getElementById('modalAgendar');
        elements.modalRate = document.getElementById('modalRate');
    }

    function loadSessionsData() {
        // Load sessions data from window object (set by backend)
        if (window.sessionsData) {
            sessions = Array.from(window.sessionsData);
        }
    }

    function attachEventListeners() {
        if (!elements.tableBody) return;

        // Event delegation for all table buttons
        elements.tableBody.addEventListener('click', handleTableClick);
    }

    function handleTableClick(e) {
        const target = e.target;

        // Open scheduler modal
        const btnScheduler = target.closest('.btn-open-scheduler');
        if (btnScheduler) {
            const sessionNumber = btnScheduler.getAttribute('data-session');
            const branchId = btnScheduler.getAttribute('data-branch-id');
            const contractedTreatmentId = btnScheduler.getAttribute('data-contracted-treatment-id');
            openSchedulerModal(sessionNumber, branchId, '', contractedTreatmentId);
            return;
        }

        // Rate session
        const btnRate = target.closest('.btn-rate');
        if (btnRate) {
            const sessionNumber = btnRate.getAttribute('data-session');
            if (window.RatingModule) {
                window.RatingModule.openRatingModal(sessionNumber);
            }
            return;
        }

        // Confirm session
        const btnConfirm = target.closest('.btn-confirm');
        if (btnConfirm) {
            const sessionNumber = btnConfirm.getAttribute('data-session');
            const appointmentId = btnConfirm.getAttribute('data-appointment-id');
            confirmSession(sessionNumber, appointmentId);
            return;
        }

        // Reschedule session
        const btnReschedule = target.closest('.btn-resched');
        if (btnReschedule) {
            const sessionNumber = btnReschedule.getAttribute('data-session');
            const branchId = btnReschedule.getAttribute('data-branch-id');
            const appointmentId = btnReschedule.getAttribute('data-appointment-id');
            const contractedTreatmentId = btnReschedule.getAttribute('data-contracted-treatment-id');
            openSchedulerModal(sessionNumber, branchId, appointmentId, contractedTreatmentId);
            return;
        }

        // Cancel session
        const btnCancel = target.closest('.btn-cancel');
        if (btnCancel) {
            const sessionNumber = btnCancel.getAttribute('data-session');
            const appointmentId = btnCancel.getAttribute('data-appointment-id');
            const cancelUrl = btnCancel.getAttribute('data-cancel-url-template');
            cancelSession(sessionNumber, appointmentId, cancelUrl);
            return;
        }
    }

    function openSchedulerModal(sessionNumber, branchId, appointmentId, contractedTreatmentId) {
        if (!elements.modalAgendar) return;

        const modal = new bootstrap.Modal(elements.modalAgendar);

        // Set session number in modal
        const sessionSpan = elements.modalAgendar.querySelector('#modalSessionNumber');
        const sessionInput = elements.modalAgendar.querySelector('#sessionNumberInput');
        const branchIdInput = elements.modalAgendar.querySelector('#branchIdInput');
        const appointmentIdInput = elements.modalAgendar.querySelector('#appointmentIdInput');
        const contractedTreatmentIdInput = elements.modalAgendar.querySelector('#contractedTreatmentIdInput');

        if (sessionSpan) sessionSpan.textContent = sessionNumber;
        if (sessionInput) sessionInput.value = sessionNumber;
        if (branchIdInput) branchIdInput.value = branchId;
        if (appointmentIdInput) appointmentIdInput.value = appointmentId;
        if (contractedTreatmentIdInput) contractedTreatmentIdInput.value = contractedTreatmentId;

        modal.show();
    }

    function confirmSession(sessionNumber, appointmentId) {
        if (!confirm('¿Confirmar que asistra a esta cita?')) return;

        // Here you would make an AJAX call to update the backend ***

    }

    function cancelSession(sessionNumber, appointmentId, cancelUrl) {
        if (!confirm('¿Estás seguro de cancelar esta cita?')) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(cancelUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (response.ok) {
                // Your controller handles the redirect, but this is a good fallback.
                window.location.reload();
            } else {
                alert('Error al cancelar la cita.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error de red.');
        });

    }

    function updateSessionRating(sessionNumber, ratingValue) {
        const row = elements.tableBody.querySelector(`tr[data-session="${sessionNumber}"]`);
        if (!row) return;

        row.setAttribute('data-review-score', ratingValue);

        // Update the button to show rated state
        const actionCell = row.querySelector('td:last-child');
        if (actionCell) {
            actionCell.innerHTML = `
                <button class="btn btn-sm btn-outline-secondary" disabled>
                    <i class="bi bi-star-fill me-1"></i>
                    <span class="d-none d-sm-inline">Calificado</span>
                </button>
            `;
        }
    }

    function reloadSessionsTable() {
        // This would typically reload data from the server
        // For now, we'll just reload the page
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    // Public API
    return {
        init: init,
        updateSessionRating: updateSessionRating,
        reloadSessionsTable: reloadSessionsTable
    };
})();

// Make functions available globally
window.initializeSessionsTable = SessionsTableModule.init;
window.updateSessionRating = SessionsTableModule.updateSessionRating;
window.reloadSessionsTable = SessionsTableModule.reloadSessionsTable;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', SessionsTableModule.init);
} else {
    SessionsTableModule.init();
}
