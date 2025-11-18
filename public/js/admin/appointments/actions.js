// Admin Appointment Actions Module
const AdminActionsModule = (function() {
    let currentAppointment = null;
    let rescheduleDate = null;
    let rescheduleTime = null;
    let rescheduleMonthRef = new Date();

    const elements = {
        modalDetail: null,
        modalBody: null,
        modalFooter: null,
        btnMarkAttended: null,
        btnConfirm: null,
        btnReschedule: null,
        btnCancel: null,
        modalReschedule: null,
        rescheduleForm: null,
        rescheduleAppointmentId: null,
        rescheduleDateInput: null,
        rescheduleTimeInput: null,
        rescheduleMonthLabel: null,
        rescheduleDays: null,
        rescheduleSelectedDate: null,
        rescheduleSlotsContainer: null,
        btnConfirmReschedule: null,
        btnReschedulePrevMonth: null,
        btnRescheduleNextMonth: null
    };

    function init() {
        cacheElements();
        attachEventListeners();
    }

    function cacheElements() {
        elements.modalDetail = document.getElementById('modalAppointmentDetail');
        elements.modalBody = document.getElementById('modalBodyAppointment');
        elements.modalFooter = document.getElementById('modalFooterAppointment');
        elements.btnMarkAttended = document.getElementById('btnMarkAttended');
        elements.btnConfirm = document.getElementById('btnConfirmAppointment');
        elements.btnReschedule = document.getElementById('btnRescheduleAppointment');
        elements.btnCancel = document.getElementById('btnCancelAppointment');
        elements.modalReschedule = document.getElementById('modalReschedule');
        elements.rescheduleForm = document.getElementById('rescheduleForm');
        elements.rescheduleAppointmentId = document.getElementById('rescheduleAppointmentId');
        elements.rescheduleDateInput = document.getElementById('rescheduleDateInput');
        elements.rescheduleTimeInput = document.getElementById('rescheduleTimeInput');
        elements.rescheduleMonthLabel = document.getElementById('rescheduleMonthLabel');
        elements.rescheduleDays = document.getElementById('rescheduleDays');
        elements.rescheduleSelectedDate = document.getElementById('rescheduleSelectedDate');
        elements.rescheduleSlotsContainer = document.getElementById('rescheduleSlotsContainer');
        elements.btnConfirmReschedule = document.getElementById('btnConfirmReschedule');
        elements.btnReschedulePrevMonth = document.getElementById('btnReschedulePrevMonth');
        elements.btnRescheduleNextMonth = document.getElementById('btnRescheduleNextMonth');
    }

    function attachEventListeners() {
        if (elements.btnMarkAttended) {
            elements.btnMarkAttended.addEventListener('click', handleMarkAttended);
        }
        if (elements.btnConfirm) {
            elements.btnConfirm.addEventListener('click', handleConfirm);
        }
        if (elements.btnReschedule) {
            elements.btnReschedule.addEventListener('click', openRescheduleModal);
        }
        if (elements.btnCancel) {
            elements.btnCancel.addEventListener('click', handleCancel);
        }
        if (elements.rescheduleForm) {
            elements.rescheduleForm.addEventListener('submit', handleRescheduleSubmit);
        }
        if (elements.btnReschedulePrevMonth) {
            elements.btnReschedulePrevMonth.addEventListener('click', () => navigateRescheduleMonth(-1));
        }
        if (elements.btnRescheduleNextMonth) {
            elements.btnRescheduleNextMonth.addEventListener('click', () => navigateRescheduleMonth(1));
        }
    }

    async function openAppointmentDetail(appointmentId) {
        const appointments = window.AdminCalendarModule.getAppointments();
        currentAppointment = appointments.find(a => a.id == appointmentId);

        if (!currentAppointment) {
            showToast('No se encontró la cita', 'danger');
            return;
        }

        // Show modal
        const modal = new bootstrap.Modal(elements.modalDetail);
        modal.show();

        // Render appointment details
        renderAppointmentDetails();
    }

    function renderAppointmentDetails() {
        if (!currentAppointment || !elements.modalBody) return;

        const schedule = new Date(currentAppointment.date + ' ' + currentAppointment.start);
        const formattedDate = schedule.toLocaleDateString('es-ES', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        elements.modalBody.innerHTML = `
        <div class="row g-3">
            <div class="col-12">
                <div class="card bg-transparent">
                    <div class="card-body">
                        <h6 class="text-secondary small text-uppercase mb-3">Información del Paciente</h6>
                        <div class="vstack gap-2">
                            <div><strong>Nombre:</strong> ${currentAppointment.patient}</div>
                            <div><strong>Email:</strong> ${currentAppointment.patient_email}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card bg-transparent">
                    <div class="card-body">
                        <h6 class="text-secondary small text-uppercase mb-3">Detalles de la Cita</h6>
                        <div class="vstack gap-2">
                            <div><strong>Tratamiento:</strong> ${currentAppointment.treatment}</div>
                            <div><strong>Sesión:</strong> #${currentAppointment.session_number}</div>
                            <div><strong>Profesional:</strong> ${currentAppointment.professional}</div>
                            <div><strong>Fecha:</strong> ${formattedDate}</div>
                            <div><strong>Hora:</strong> ${currentAppointment.start} ${currentAppointment.duration ? `(${currentAppointment.duration} min)` : ''}</div>
                            <div><strong>Estado:</strong> <span class="badge text-bg-${getStatusVariant(currentAppointment.status)}">${currentAppointment.status}</span></div>

                            ${(currentAppointment.zones?.big?.length > 0) ? `
                                <div>
                                    <strong>Zonas grandes:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        ${currentAppointment.zones.big.map(zone => `<span class="badge bg-secondary">${zone}</span>`).join('')}
                                    </div>
                                </div>
                            ` : ''}

                            ${(currentAppointment.zones?.mini?.length > 0) ? `
                                <div>
                                    <strong>Mini zonas:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        ${currentAppointment.zones.mini.map(zone => `<span class="badge bg-light text-dark">${zone}</span>`).join('')}
                                    </div>
                                </div>
                            ` : ''}

                            ${currentAppointment.attended !== null ? `<div><strong>Asistencia:</strong> <span class="badge text-bg-${currentAppointment.attended ? 'success' : 'danger'}">${currentAppointment.attended ? 'Asistió' : 'No asistió'}</span></div>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

        // Show/hide action buttons based on status
        updateActionButtons();
    }

    function updateActionButtons() {
        if (!currentAppointment) return;

        const canMarkAttended = currentAppointment.status === 'Confirmada' && currentAppointment.attended === null;
        const canConfirm = currentAppointment.status === 'Por confirmar';
        const canReschedule = ['Por confirmar', 'Confirmada'].includes(currentAppointment.status);
        const canCancel = currentAppointment.status !== 'Cancelada' && currentAppointment.status !== 'Atendida';

        if (elements.btnMarkAttended) {
            elements.btnMarkAttended.classList.toggle('d-none', !canMarkAttended);
        }
        if (elements.btnConfirm) {
            elements.btnConfirm.classList.toggle('d-none', !canConfirm);
        }
        if (elements.btnReschedule) {
            elements.btnReschedule.classList.toggle('d-none', !canReschedule);
        }
        if (elements.btnCancel) {
            elements.btnCancel.classList.toggle('d-none', !canCancel);
        }
    }

    async function handleMarkAttended() {
        if (!currentAppointment) return;

        if (!confirm('¿Confirmar que el paciente asistió a esta cita? Se asignará un profesional automáticamente.')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = window.apiEndpoints.markAsAttended.replace(':id', currentAppointment.id);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ attended: true })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast('Cita marcada como atendida exitosamente');
                closeModal();
                reloadCalendar();
            } else {
                showToast(data.message || 'Error al marcar la cita', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de red al procesar la solicitud', 'danger');
        }
    }

    async function handleConfirm() {
        if (!currentAppointment) return;

        if (!confirm('¿Confirmar esta cita?')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = window.apiEndpoints.confirmAppointment.replace(':id', currentAppointment.id);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast('Cita confirmada exitosamente');
                closeModal();
                reloadCalendar();
            } else {
                showToast(data.message || 'Error al confirmar la cita', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de red al procesar la solicitud', 'danger');
        }
    }

    async function handleCancel() {
        if (!currentAppointment) return;

        if (!confirm('¿Está seguro de cancelar esta cita? Esta acción no se puede deshacer.')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = window.apiEndpoints.cancel.replace(':id', currentAppointment.id);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast('Cita cancelada exitosamente');
                closeModal();
                reloadCalendar();
            } else {
                showToast(data.message || 'Error al cancelar la cita', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de red al procesar la solicitud', 'danger');
        }
    }

    function openRescheduleModal() {
        if (!currentAppointment) return;

        rescheduleDate = null;
        rescheduleTime = null;
        rescheduleMonthRef = new Date();

        if (elements.rescheduleAppointmentId) {
            elements.rescheduleAppointmentId.value = currentAppointment.id;
        }

        renderRescheduleCalendar();

        const modal = new bootstrap.Modal(elements.modalReschedule);
        modal.show();
    }

    function navigateRescheduleMonth(direction) {
        rescheduleMonthRef.setMonth(rescheduleMonthRef.getMonth() + direction);
        renderRescheduleCalendar();
    }

    function renderRescheduleCalendar() {
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        if (elements.rescheduleMonthLabel) {
            elements.rescheduleMonthLabel.textContent = `${monthNames[rescheduleMonthRef.getMonth()]} ${rescheduleMonthRef.getFullYear()}`;
        }

        if (!elements.rescheduleDays) return;

        elements.rescheduleDays.innerHTML = '';

        const firstDay = new Date(rescheduleMonthRef.getFullYear(), rescheduleMonthRef.getMonth(), 1);
        const startDay = (firstDay.getDay() + 6) % 7;
        const daysInMonth = new Date(rescheduleMonthRef.getFullYear(), rescheduleMonthRef.getMonth() + 1, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Empty cells
        for (let i = 0; i < startDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-cell cell-muted';
            empty.style.visibility = 'hidden';
            elements.rescheduleDays.appendChild(empty);
        }

        // Days
        for (let d = 1; d <= daysInMonth; d++) {
            const date = new Date(rescheduleMonthRef.getFullYear(), rescheduleMonthRef.getMonth(), d);
            const cell = document.createElement('div');
            cell.className = 'calendar-cell';
            cell.textContent = d;

            if (date < today) {
                cell.classList.add('cell-muted');
                cell.style.pointerEvents = 'none';
            }

            if (rescheduleDate && sameYMD(date, rescheduleDate)) {
                cell.classList.add('cell-selected');
            }

            cell.addEventListener('click', () => selectRescheduleDate(date));
            elements.rescheduleDays.appendChild(cell);
        }
    }

    async function selectRescheduleDate(date) {
        rescheduleDate = date;
        rescheduleTime = null;

        if (elements.rescheduleSelectedDate) {
            elements.rescheduleSelectedDate.textContent = date.toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        if (elements.rescheduleDateInput) {
            elements.rescheduleDateInput.value = formatYmd(date);
        }

        if (elements.btnConfirmReschedule) {
            elements.btnConfirmReschedule.disabled = true;
        }

        renderRescheduleCalendar();
        await loadRescheduleSlots(date);
    }

    async function loadRescheduleSlots(date) {
        if (!elements.rescheduleSlotsContainer) return;

        elements.rescheduleSlotsContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('appointments/available-slots', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    date: formatYmd(date),
                    branch_id: currentAppointment.branch_id,
                })
            });

            const data = await response.json();
            const slots = data.slots || [];

            elements.rescheduleSlotsContainer.innerHTML = '';

            if (slots.length === 0) {
                elements.rescheduleSlotsContainer.innerHTML = '<div class="text-secondary text-center py-4">No hay horarios disponibles</div>';
                return;
            }

            slots.forEach(time => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-light slot-btn';
                btn.textContent = time;
                btn.addEventListener('click', () => selectRescheduleTime(time));
                elements.rescheduleSlotsContainer.appendChild(btn);
            });
        } catch (error) {
            console.error('Error loading slots:', error);
            elements.rescheduleSlotsContainer.innerHTML = '<div class="text-danger text-center py-4">Error al cargar horarios</div>';
        }
    }

    function selectRescheduleTime(time) {
        rescheduleTime = time;

        if (elements.rescheduleTimeInput) {
            elements.rescheduleTimeInput.value = time;
        }

        const buttons = elements.rescheduleSlotsContainer.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-light');
            if (btn.textContent === time) {
                btn.classList.remove('btn-outline-light');
                btn.classList.add('btn-primary');
            }
        });

        if (elements.btnConfirmReschedule) {
            elements.btnConfirmReschedule.disabled = false;
        }
    }

    async function handleRescheduleSubmit(e) {
        e.preventDefault();

        if (!rescheduleDate || !rescheduleTime) {
            showToast('Debe seleccionar fecha y hora', 'danger');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = window.apiEndpoints.reschedule.replace(':id', currentAppointment.id);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    appointment_date: formatYmd(rescheduleDate),
                    appointment_time: rescheduleTime
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast('Cita reagendada exitosamente');
                const modal = bootstrap.Modal.getInstance(elements.modalReschedule);
                if (modal) modal.hide();
                closeModal();
                reloadCalendar();
            } else {
                showToast(data.message || 'Error al reagendar la cita', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de red al procesar la solicitud', 'danger');
        }
    }

    function getStatusVariant(status) {
        const map = {
            'Confirmada': 'success',
            'Por confirmar': 'warning',
            'Atendida': 'info',
            'Cancelada': 'danger'
        };
        return map[status] || 'secondary';
    }

    function sameYMD(date1, date2) {
        return date1 && date2 &&
            date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate();
    }

    function formatYmd(date) {
        const pad = n => String(n).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    function closeModal() {
        const modal = bootstrap.Modal.getInstance(elements.modalDetail);
        if (modal) modal.hide();
    }

    function reloadCalendar() {
        if (window.AdminCalendarModule) {
            window.AdminCalendarModule.reload();
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `position-fixed bottom-0 end-0 m-3 p-2 px-3 rounded bg-${type === 'danger' ? 'danger' : 'dark'} border border-secondary-subtle text-white`;
        toast.style.zIndex = '2000';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }

    return {
        init: init,
        openAppointmentDetail: openAppointmentDetail
    };
})();

// Initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', AdminActionsModule.init);
} else {
    AdminActionsModule.init();
}

window.AdminActionsModule = AdminActionsModule;
