// Sessions table module for handling session interactions
const SessionsTableModule = (function() {
    const elements = {
        tableBody: null,
        modalAgendar: null,
        modalRate: null,
        modalActions: null
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
        elements.modalActions = document.getElementById('appointmentActionsModal');
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

        // Rate session
        const btnRate = target.closest('.btn-rate');
        if (btnRate) {
            const sessionNumber = btnRate.getAttribute('data-session');
            const rateUrl = btnRate.getAttribute('data-rate-url-template');
            if (window.RatingModule) {
                window.RatingModule.openRatingModal(sessionNumber, rateUrl);
            }
            return;
        }

        // Confirm session
        const btnConfirm = target.closest('.btn-confirm');
        if (btnConfirm) {
            const confirmUrl = btnConfirm.getAttribute('data-confirm-url-template');
            confirmSession(confirmUrl);
            return;
        }

        // Cancel session
        const btnCancel = target.closest('.btn-cancel');
        if (btnCancel) {
            const cancelUrl = btnCancel.getAttribute('data-cancel-url-template');
            cancelSession(cancelUrl);
            return;
        }

        // Open scheduler modal
        const btnScheduler = target.closest('.btn-open-scheduler');
        if (btnScheduler) {
            const sessionNumber = btnScheduler.getAttribute('data-session');
            const branchId = btnScheduler.getAttribute('data-branch-id');
            const contractedTreatmentId = btnScheduler.getAttribute('data-contracted-treatment-id');
            const storeUrl = btnScheduler.getAttribute('data-store-url-template');
            openSchedulerModal(sessionNumber, branchId, storeUrl, contractedTreatmentId);
            return;
        }

        // Reschedule session
        const btnReschedule = target.closest('.btn-resched');
        if (btnReschedule) {
            const branchId = btnReschedule.getAttribute('data-branch-id');
            const sessionNumber = btnReschedule.getAttribute('data-session');
            const reschedUrl = btnReschedule.getAttribute('data-resched-url-template');
            openSchedulerModal(sessionNumber, branchId, reschedUrl, '');
            return;
        }

        // Open appointment actions modal
        const btnActions = target.closest('.btn-open-appointment-actions');
        if (btnActions) {
            const sessionNumber = btnActions.getAttribute('data-session');
            const dateFormatted = btnActions.getAttribute('data-date-formatted');
            const time = btnActions.getAttribute('data-time');
            const status = btnActions.getAttribute('data-status');
            const canManage = btnActions.getAttribute('data-can-manage') === 'true';
            const isConfirmed = btnActions.getAttribute('data-is-confirmed') === 'true';
            const isNearLimit = btnActions.getAttribute('data-is-near-limit') === 'true';
            const confirmUrl = btnActions.getAttribute('data-confirm-url');
            const reschedUrl = btnActions.getAttribute('data-resched-url');
            const cancelUrl = btnActions.getAttribute('data-cancel-url');
            const branchId = btnActions.getAttribute('data-branch-id');

            openActionsModal(sessionNumber, dateFormatted, time, status, canManage, isConfirmed, isNearLimit, confirmUrl, reschedUrl, cancelUrl, branchId);
            return;
        }
    }

    function openActionsModal(sessionNumber, dateFormatted, time, status, canManage, isConfirmed, isNearLimit, confirmUrl, reschedUrl, cancelUrl, branchId) {
        if (!elements.modalActions) return;

        // Set labels
        elements.modalActions.querySelector('#actionsModalSessionNumber').textContent = sessionNumber;
        elements.modalActions.querySelector('#actionsModalDateTime').textContent = `${dateFormatted} - ${time}`;

        const statusBadge = elements.modalActions.querySelector('#actionsModalStatus');
        statusBadge.textContent = status;
        if (status === 'Confirmada') {
            statusBadge.className = 'badge bg-success text-white';
        } else {
            statusBadge.className = 'badge bg-info text-white';
        }

        const btnConfirm = elements.modalActions.querySelector('#btnConfirmAction');
        const btnResched = elements.modalActions.querySelector('#btnReschedAction');
        const btnCancel = elements.modalActions.querySelector('#btnCancelAction');
        const noActionsMsg = elements.modalActions.querySelector('#noActionsMessage');

        // Reset display
        btnConfirm.classList.add('d-none');
        btnResched.classList.add('d-none');
        btnCancel.classList.add('d-none');
        noActionsMsg.classList.add('d-none');

        if (canManage) {
            // Confirm action (only if near limit [less than 48h] and not confirmed yet)
            if (!isConfirmed && isNearLimit) {
                btnConfirm.classList.remove('d-none');
                btnConfirm.onclick = function() {
                    const modalInst = bootstrap.Modal.getInstance(elements.modalActions);
                    if (modalInst) modalInst.hide();
                    confirmSession(confirmUrl);
                };
            }

            // Reschedule action
            btnResched.classList.remove('d-none');
            btnResched.onclick = function() {
                const modalInst = bootstrap.Modal.getInstance(elements.modalActions);
                if (modalInst) modalInst.hide();
                openSchedulerModal(sessionNumber, branchId, reschedUrl, '');
            };

            // Cancel action
            btnCancel.classList.remove('d-none');
            btnCancel.onclick = function() {
                const modalInst = bootstrap.Modal.getInstance(elements.modalActions);
                if (modalInst) modalInst.hide();
                cancelSession(cancelUrl);
            };
        } else {
            noActionsMsg.classList.remove('d-none');
        }

        // Show modal
        const modal = bootstrap.Modal.getOrCreateInstance(elements.modalActions);
        modal.show();
    }

    function openSchedulerModal(sessionNumber, branchId, url, contractedTreatmentId) {
        if (!elements.modalAgendar) return;

        // Set session number in modal
        const sessionSpan = elements.modalAgendar.querySelector('#modalSessionNumber');
        const sessionInput = elements.modalAgendar.querySelector('#sessionNumberInput');
        const branchIdInput = elements.modalAgendar.querySelector('#branchIdInput');
        const appointmentForm = elements.modalAgendar.querySelector('#appointmentForm');
        const contractedTreatmentIdInput = elements.modalAgendar.querySelector('#contractedTreatmentIdInput');

        if (sessionSpan) sessionSpan.textContent = sessionNumber;
        if (sessionInput) sessionInput.value = sessionNumber;
        if (branchIdInput) branchIdInput.value = branchId;
        if (appointmentForm) appointmentForm.action = url;
        if (contractedTreatmentIdInput) contractedTreatmentIdInput.value = contractedTreatmentId;

        // Show the modal
        const modal = bootstrap.Modal.getOrCreateInstance(elements.modalAgendar);
        modal.show();

        setTimeout(()=>{
            const todayCell = document.querySelector('#calendarDays .cell-today');
            if(todayCell) todayCell.click();
        },500);
    }

    function confirmSession(confirmUrl) {

        if (!confirm('¿Confirma que asistra a esta cita?')) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(confirmUrl, {
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
                alert('Error al confirmar la cita.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error de red.');
        });

    }

    function cancelSession(cancelUrl) {

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
