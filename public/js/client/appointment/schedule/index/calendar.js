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

    async function renderSlotsForDate(date) { // 1. Marcar la función como async
        if (!elements.slotsContainer) return;

        // (Opcional pero recomendado) Mostrar un estado de carga
        elements.slotsContainer.innerHTML = `
            <div class="text-secondary text-center py-4">
                Cargando horarios...
            </div>`;
        if (elements.btnConfirm) {
            elements.btnConfirm.disabled = true;
        }

        // 2. Usar await para esperar a que la promesa se resuelva
        const slots = await generateSlotsFor(date);

        // A partir de aquí, el resto de tu código funciona perfectamente
        // porque 'slots' ahora sí es el array que esperabas.
        elements.slotsContainer.innerHTML = ''; // Limpiar el mensaje de "Cargando..."

        if (!slots || !slots.length) { // Añadida una comprobación extra por si algo sale mal
            elements.slotsContainer.innerHTML = `
                <div class="text-secondary text-center py-4">
                    No hay horarios disponibles para este día.
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

    /**
     * Fetches available appointment slots from the backend for a given date.
     * @param {Date} date The selected date.
     * @returns {Promise<string[]>} A promise that resolves to an array of available time slots.
     */
    async function generateSlotsFor(date) {
        const formattedDate = formatDateISO(date);
        const branchId = document.getElementById('branchIdInput').value;
        // The URL for our API endpoint. It's better to get this from Blade if possible.
        const apiUrl = '/api/appointments/available-slots';

        // Get the CSRF token from the meta tag in the head
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    date: formattedDate,
                    branch_id: branchId
                })
            });

            if (!response.ok) {
                // Handle HTTP errors like 404, 500, etc.
                const errorData = await response.json();
                console.error('Error fetching slots:', response.status, errorData);
                // You might want to show an error message to the user here
                return []; // Return an empty array on failure
            }

            const data = await response.json();

            // The controller returns { slots: [...] }, so we return data.slots
            return data.slots || []; // Return slots or an empty array if not present

        } catch (error) {
            // Handle network errors or other exceptions
            console.error('An unexpected error occurred:', error);
            // You might want to show an error message to the user here
            return []; // Return an empty array on failure
        }
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

    /**
     * Formats a Date object into a 'YYYY-MM-DD' string.
     * @param {Date} date The date to format.
     * @returns {string}
     */
    function formatDateISO(date) {
        // A robust way to get YYYY-MM-DD in the local timezone
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
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
