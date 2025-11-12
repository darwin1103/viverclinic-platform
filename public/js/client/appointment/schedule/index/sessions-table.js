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
            openSchedulerModal(sessionNumber, branchId);
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
            confirmSession(sessionNumber);
            return;
        }

        // Reschedule session
        const btnReschedule = target.closest('.btn-resched');
        if (btnReschedule) {
            const sessionNumber = btnReschedule.getAttribute('data-session');
            const branchId = btnReschedule.getAttribute('data-branch-id');
            openSchedulerModal(sessionNumber, branchId);
            return;
        }

        // Cancel session
        const btnCancel = target.closest('.btn-cancel');
        if (btnCancel) {
            const sessionNumber = btnCancel.getAttribute('data-session');
            cancelSession(sessionNumber);
            return;
        }
    }

    function openSchedulerModal(sessionNumber, branchId) {
        if (!elements.modalAgendar) return;

        const modal = new bootstrap.Modal(elements.modalAgendar);

        // Set session number in modal
        const sessionSpan = elements.modalAgendar.querySelector('#modalSessionNumber');
        const sessionInput = elements.modalAgendar.querySelector('#sessionNumberInput');
        const branchIdInput = elements.modalAgendar.querySelector('#branchIdInput');

        if (sessionSpan) sessionSpan.textContent = sessionNumber;
        if (sessionInput) sessionInput.value = sessionNumber;
        if (branchIdInput) branchIdInput.value = branchId;

        modal.show();
    }

    function confirmSession(sessionNumber) {
        if (!confirm('¿Confirmar que asistió a esta cita?')) return;

        // Here you would make an AJAX call to update the backend
        // For now, we'll simulate it
        updateSessionInDOM(sessionNumber, {
            attended: true,
            status: 'ok'
        });

        showToast('Cita confirmada como asistida');
    }

    function cancelSession(sessionNumber) {
        if (!confirm('¿Estás seguro de cancelar esta cita?')) return;

        // Here you would make an AJAX call to update the backend
        // For now, we'll simulate it
        updateSessionInDOM(sessionNumber, {
            date: null,
            time: null,
            status: 'pending'
        });

        showToast('Cita cancelada exitosamente');
    }

    function updateSessionInDOM(sessionNumber, updates) {
        const row = elements.tableBody.querySelector(`tr[data-session="${sessionNumber}"]`);
        if (!row) return;

        // Update row attributes
        if (updates.status) row.setAttribute('data-status', updates.status);
        if (updates.date !== undefined) row.setAttribute('data-date', updates.date || '');
        if (updates.time !== undefined) row.setAttribute('data-time', updates.time || '');

        // Reload the table to reflect changes
        // In a real application, you would reload from the server
        setTimeout(() => {
            window.location.reload();
        }, 1000);
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

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 m-3 p-2 px-3 rounded bg-dark border border-secondary-subtle';
        toast.style.zIndex = '2000';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 1800);
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
