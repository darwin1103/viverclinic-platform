// Calendar module for appointment scheduling
const CalendarModule = (function() {
    const monthNames = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    let today = new Date();
    today.setHours(0, 0, 0, 0);

    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();
    let selectedDate = null;
    let selectedSlot = null;
    let currentSessionNumber = null;

    const elements = {
        modal: null,
        lblMonthYear: null,
        calendarDays: null,
        lblSelectedDate: null,
        slotsContainer: null,
        btnConfirm: null,
        btnPrevMonth: null,
        btnNextMonth: null,
        sessionNumberSpan: null,
        sessionNumberInput: null,
        dateInput: null,
        timeInput: null,
        form: null
    };

    function init() {
        cacheElements();
        attachEventListeners();
    }

    function cacheElements() {
        elements.modal = document.getElementById('modalAgendar');
        elements.lblMonthYear = document.getElementById('lblMonthYear');
        elements.calendarDays = document.getElementById('calendarDays');
        elements.lblSelectedDate = document.getElementById('lblSelectedDate');
        elements.slotsContainer = document.getElementById('slotsContainer');
        elements.btnConfirm = document.getElementById('btnConfirm');
        elements.btnPrevMonth = document.getElementById('btnPrevMonth');
        elements.btnNextMonth = document.getElementById('btnNextMonth');
        elements.sessionNumberSpan = document.getElementById('modalSessionNumber');
        elements.sessionNumberInput = document.getElementById('sessionNumberInput');
        elements.dateInput = document.getElementById('appointmentDateInput');
        elements.timeInput = document.getElementById('appointmentTimeInput');
        elements.form = document.getElementById('appointmentForm');
    }

    function attachEventListeners() {
        if (elements.btnPrevMonth) {
            elements.btnPrevMonth.addEventListener('click', navigateToPreviousMonth);
        }

        if (elements.btnNextMonth) {
            elements.btnNextMonth.addEventListener('click', navigateToNextMonth);
        }

        if (elements.modal) {
            elements.modal.addEventListener('show.bs.modal', handleModalShow);
            elements.modal.addEventListener('hidden.bs.modal', handleModalHidden);
        }

        if (elements.form) {
            elements.form.addEventListener('submit', handleFormSubmit);
        }
    }

    function navigateToPreviousMonth() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentYear, currentMonth);
    }

    function navigateToNextMonth() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentYear, currentMonth);
    }

    function handleModalShow(event) {
        const button = event.relatedTarget;
        if (button) {
            currentSessionNumber = button.getAttribute('data-session');
            if (elements.sessionNumberSpan) {
                elements.sessionNumberSpan.textContent = currentSessionNumber;
            }
            if (elements.sessionNumberInput) {
                elements.sessionNumberInput.value = currentSessionNumber;
            }
        }

        resetSelection();
        renderCalendar(today.getFullYear(), today.getMonth());
    }

    function handleModalHidden() {
        resetSelection();
    }

    function handleFormSubmit(e) {
        e.preventDefault();

        if (!selectedDate || !selectedSlot) {
            showToast('Por favor selecciona una fecha y hora');
            return;
        }

        // Here you would normally submit the form
        // For now, we'll simulate it with a toast
        const formData = new FormData(elements.form);

        showToast('Cita agendada exitosamente');

        // Close modal
        const modal = bootstrap.Modal.getInstance(elements.modal);
        if (modal) {
            modal.hide();
        }

        // Reload table (this would be handled by the sessions-table.js)
        if (window.reloadSessionsTable) {
            window.reloadSessionsTable();
        }
    }

    function resetSelection() {
        selectedDate = null;
        selectedSlot = null;
        currentSessionNumber = null;
        updateSelectedLabels();
        if (elements.btnConfirm) {
            elements.btnConfirm.disabled = true;
        }
    }

    function renderCalendar(year, month) {
        if (!elements.lblMonthYear || !elements.calendarDays) return;

        elements.lblMonthYear.textContent = `${monthNames[month]} ${year}`;
        elements.calendarDays.innerHTML = '';

        const firstDay = new Date(year, month, 1);
        const startDay = (firstDay.getDay() + 6) % 7; // Monday = 0
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Empty cells for days before month starts
        for (let i = 0; i < startDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-cell cell-muted';
            empty.style.visibility = 'hidden';
            elements.calendarDays.appendChild(empty);
        }

        // Days of the month
        for (let d = 1; d <= daysInMonth; d++) {
            const date = new Date(year, month, d);
            const cell = document.createElement('div');
            cell.className = 'calendar-cell';
            cell.textContent = d;

            // Disable past dates
            if (date < today) {
                cell.classList.add('cell-muted');
                cell.style.pointerEvents = 'none';
            }

            // Highlight today
            if (date.getTime() === today.getTime()) {
                cell.classList.add('cell-today');
            }

            // Disable Sundays
            if (date.getDay() === 0) {
                cell.classList.add('cell-muted');
                cell.style.pointerEvents = 'none';
            }

            // Highlight selected date
            if (selectedDate && sameYMD(date, selectedDate)) {
                cell.classList.add('cell-selected');
            }

            cell.addEventListener('click', () => {
                selectDate(date);
            });

            elements.calendarDays.appendChild(cell);
        }

        // If selected date is not in current month, clear slots
        if (!selectedDate || selectedDate.getMonth() !== month || selectedDate.getFullYear() !== year) {
            clearSlots();
        }
    }

    function selectDate(date) {
        selectedDate = date;
        selectedSlot = null;

        updateSelectedLabels();
        renderCalendar(currentYear, currentMonth);
        renderSlotsForDate(date);

        // Update hidden input
        if (elements.dateInput) {
            elements.dateInput.value = formatDateISO(date);
        }
    }

    function updateSelectedLabels() {
        if (!elements.lblSelectedDate) return;

        if (selectedDate) {
            elements.lblSelectedDate.textContent = selectedDate.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } else {
            elements.lblSelectedDate.textContent = 'Selecciona un día';
        }
    }

    function renderSlotsForDate(date) {
        if (!elements.slotsContainer) return;

        elements.slotsContainer.innerHTML = '';
        const slots = generateSlotsFor(date);

        if (!slots.length) {
            elements.slotsContainer.innerHTML = `
                <div class="text-secondary text-center py-4">
                    No hay horarios disponibles para este día
                </div>`;
            if (elements.btnConfirm) {
                elements.btnConfirm.disabled = true;
            }
            return;
        }

        slots.forEach(time => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-light slot-btn';
            btn.textContent = time;

            if (selectedSlot === time) {
                btn.classList.remove('btn-outline-light');
                btn.classList.add('btn-primary');
            }

            btn.addEventListener('click', () => {
                selectSlot(time);
            });

            elements.slotsContainer.appendChild(btn);
        });
    }

    function selectSlot(time) {
        selectedSlot = time;

        // Update UI
        const allSlotButtons = elements.slotsContainer.querySelectorAll('button');
        allSlotButtons.forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-light');
        });

        const selectedButton = Array.from(allSlotButtons).find(btn => btn.textContent === time);
        if (selectedButton) {
            selectedButton.classList.remove('btn-outline-light');
            selectedButton.classList.add('btn-primary');
        }

        // Enable confirm button
        if (elements.btnConfirm) {
            elements.btnConfirm.disabled = false;
        }

        // Update hidden input
        if (elements.timeInput) {
            elements.timeInput.value = time;
        }
    }

    function generateSlotsFor(date) {
        const day = date.getDay();

        // No slots for Sundays
        if (day === 0) return [];

        const start = new Date(date);
        start.setHours(9, 0, 0, 0);

        const end = new Date(date);
        end.setHours(17, 0, 0, 0);

        // Blocked times (lunch break, etc.)
        const blocked = ['12:30', '13:00', '13:30', '14:00', '14:30', '15:00'];

        const slots = [];
        const currentTime = new Date(start);

        while (currentTime < end) {
            const timeStr = currentTime.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });

            // Skip if it's today and time has passed
            if (sameYMD(date, today) && currentTime < new Date()) {
                currentTime.setMinutes(currentTime.getMinutes() + 30);
                continue;
            }

            // Skip blocked times
            if (!blocked.includes(timeStr)) {
                slots.push(timeStr);
            }

            currentTime.setMinutes(currentTime.getMinutes() + 30);
        }

        return slots;
    }

    function clearSlots() {
        if (elements.slotsContainer) {
            elements.slotsContainer.innerHTML = `
                <div class="text-secondary text-center py-4">
                    Selecciona un día para ver horarios disponibles
                </div>`;
        }
        if (elements.btnConfirm) {
            elements.btnConfirm.disabled = true;
        }
    }

    function sameYMD(date1, date2) {
        return date1 && date2 &&
            date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate();
    }

    function formatDateISO(date) {
        return date.toISOString().split('T')[0];
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 m-3 p-2 px-3 rounded bg-dark border border-secondary-subtle';
        toast.style.zIndex = '2000';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 1800);
    }

    return {
        init: init
    };
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', CalendarModule.init);
} else {
    CalendarModule.init();
}
