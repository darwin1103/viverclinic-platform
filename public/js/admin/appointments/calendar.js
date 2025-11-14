// Admin Calendar Module
const AdminCalendarModule = (function() {
    // Utility functions
    const pad = n => String(n).padStart(2, '0');
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const dayNamesShort = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

    // Date utilities
    function formatYmd(date) {
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    function parseYmd(str) {
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function mondayOf(date) {
        const d = new Date(date);
        d.setHours(0, 0, 0, 0);
        const k = (d.getDay() + 6) % 7;
        d.setDate(d.getDate() - k);
        return d;
    }

    function formatHuman(date, options = {}) {
        return date.toLocaleDateString('es-ES', options);
    }

    function endTime(start, duration) {
        const [h, m] = start.split(':').map(Number);
        const d = new Date();
        d.setHours(h, m, 0, 0);
        d.setMinutes(d.getMinutes() + duration);
console.log(h)
console.log(m)
        return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }

    // State
    let today = new Date();
    today.setHours(0, 0, 0, 0);
    let currentStart = mondayOf(today);
    let currentMobileDate = new Date(today);
    let appointments = [];
    let isMobile = window.innerWidth < 992;

    // DOM Elements
    const elements = {
        fiveGrid: document.getElementById('fiveGrid'),
        mobileList: document.getElementById('mobileAppointmentsList'),
        mobileDayName: document.getElementById('mobileDayName'),
        mobileDayNumber: document.getElementById('mobileDayNumber'),
        mobileTotalCount: document.getElementById('mobileTotalCount'),
        mobileDateLabel: document.getElementById('mobileDateLabel'),
        loadingState: document.getElementById('loadingState'),
        emptyState: document.getElementById('emptyState'),
        btnPrev5: document.getElementById('btnPrev5'),
        btnNext5: document.getElementById('btnNext5'),
        btnPrevMobile: document.getElementById('btnPrevMobile'),
        btnNextMobile: document.getElementById('btnNextMobile'),
        btnOpenRange: document.getElementById('btnOpenRange'),
        btnOpenRangeMobile: document.getElementById('btnOpenRangeMobile')
    };

    function init() {
        attachEventListeners();
        renderRangeLabel();
        loadAppointments();

        // Handle window resize
        window.addEventListener('resize', handleResize);
    }

    function attachEventListeners() {
        if (elements.btnPrev5) {
            elements.btnPrev5.addEventListener('click', navigatePrevWeek);
        }
        if (elements.btnNext5) {
            elements.btnNext5.addEventListener('click', navigateNextWeek);
        }
        if (elements.btnPrevMobile) {
            elements.btnPrevMobile.addEventListener('click', navigatePrevDay);
        }
        if (elements.btnNextMobile) {
            elements.btnNextMobile.addEventListener('click', navigateNextDay);
        }
    }

    function handleResize() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth < 992;
        if (wasMobile !== isMobile) {
            renderCalendar();
        }
    }

    function navigatePrevWeek() {
        currentStart.setDate(currentStart.getDate() - 7);
        renderRangeLabel();
        loadAppointments();
    }

    function navigateNextWeek() {
        currentStart.setDate(currentStart.getDate() + 7);
        renderRangeLabel();
        loadAppointments();
    }

    function navigatePrevDay() {
        currentMobileDate.setDate(currentMobileDate.getDate() - 1);
        renderMobileView();
    }

    function navigateNextDay() {
        currentMobileDate.setDate(currentMobileDate.getDate() + 1);
        renderMobileView();
    }

    function renderRangeLabel() {
        if (!elements.btnOpenRange) return;

        const end = new Date(currentStart);
        end.setDate(end.getDate() + 6);

        const isThisWeek =
            formatYmd(currentStart) === formatYmd(mondayOf(today)) &&
            currentStart.getDay() === 1 && end.getDay() === 7;

        if (isThisWeek) {
            elements.btnOpenRange.textContent = 'Esta semana';
        } else {
            const startTxt = formatHuman(currentStart, { day: '2-digit', month: 'short' }).replace('.', '');
            const endTxt = formatHuman(end, { day: '2-digit', month: 'short' }).replace('.', '');
            elements.btnOpenRange.textContent = `${startTxt} – ${endTxt}`;
        }
    }

    async function loadAppointments() {
        showLoading(true);

        const startDate = formatYmd(currentStart);
        const endDate = new Date(currentStart);
        endDate.setDate(endDate.getDate() + (isMobile ? 0 : 6));
        const endDateStr = formatYmd(endDate);

        try {
            const filters = window.AdminFiltersModule ? window.AdminFiltersModule.getFilters() : {};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(window.apiEndpoints.fetchAppointments, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDateStr,
                    branch_id: window.branchId,
                    ...filters
                })
            });

            if (!response.ok) {
                throw new Error('Error al cargar las citas');
            }

            const data = await response.json();
            appointments = data.appointments || [];
            renderCalendar();
        } catch (error) {
            console.error('Error loading appointments:', error);
            showToast('Error al cargar las citas', 'danger');
            appointments = [];
            renderCalendar();
        } finally {
            showLoading(false);
        }
    }

    function renderCalendar() {
        if (isMobile) {
            renderMobileView();
        } else {
            renderDesktopView();
        }
    }

    function renderDesktopView() {
        if (!elements.fiveGrid) return;

        elements.fiveGrid.innerHTML = '';

        const cols = [];
        for (let i = 0; i < 7; i++) {
            const d = new Date(currentStart);
            d.setDate(d.getDate() + i);

            const col = document.createElement('div');
            col.className = 'day-col';
            col.dataset.date = formatYmd(d);
            col.innerHTML = `
                <div class="day-head">
                    <span class="text-secondary text-uppercase small">${dayNamesShort[i]}</span>
                    <span class="fw-semibold">${d.getDate()}</span>
                </div>
                <div class="day-body"></div>
            `;
            elements.fiveGrid.appendChild(col);
            cols.push(col);
        }

        // Group appointments by date
        const byDay = {};
        appointments.forEach(a => {
            if (!byDay[a.date]) byDay[a.date] = [];
            byDay[a.date].push(a);
        });

        // Render appointments in each column
        let hasAppointments = false;
        cols.forEach(col => {
            const dateKey = col.dataset.date;
            const body = col.querySelector('.day-body');
            const items = (byDay[dateKey] || []).sort((a, b) => a.start.localeCompare(b.start));

            if (items.length === 0) {
                body.innerHTML = '<div class="empty-state small">Sin citas</div>';
                return;
            }

            hasAppointments = true;
            items.forEach(a => {
                const card = createAppointmentCard(a);
                body.appendChild(card);
            });
        });

        // Show/hide empty state
        if (elements.emptyState) {
            elements.emptyState.classList.toggle('d-none', hasAppointments);
        }
    }

    function renderMobileView() {
        if (!elements.mobileList) return;

        // Update date label
        if (elements.mobileDayName) {
            elements.mobileDayName.textContent = formatHuman(currentMobileDate, { weekday: 'long' });
        }
        if (elements.mobileDayNumber) {
            elements.mobileDayNumber.textContent = currentMobileDate.getDate();
        }
        if (elements.mobileDateLabel) {
            elements.mobileDateLabel.textContent = formatHuman(currentMobileDate, {
                day: 'numeric',
                month: 'short'
            }).replace('.', '');
        }

        // Filter appointments for current date
        const dateKey = formatYmd(currentMobileDate);
        const dayAppointments = appointments
            .filter(a => a.date === dateKey)
            .sort((a, b) => a.start.localeCompare(b.start));

        // Update count
        if (elements.mobileTotalCount) {
            elements.mobileTotalCount.textContent = dayAppointments.length;
        }

        // Render appointments
        elements.mobileList.innerHTML = '';
        if (dayAppointments.length === 0) {
            elements.mobileList.innerHTML = '<div class="empty-state">Sin citas para este día</div>';
            return;
        }

        dayAppointments.forEach(a => {
            const card = createAppointmentCard(a);
            elements.mobileList.appendChild(card);
        });
    }

    function createAppointmentCard(appointment) {
        const card = document.createElement('div');
        card.className = `appt-card ${getStatusClass(appointment.status)}`;
        card.dataset.id = appointment.id;

        card.innerHTML = `
            <div class="title">${appointment.patient}</div>
            <div class="meta">
                <span>${appointment.professional}</span>
                <span>${appointment.start}${appointment.duration ? `–${endTime(appointment.start, appointment.duration)}` : ''}</span>
            </div>
            <div class="mt-1 d-flex justify-content-between align-items-center">
                <span class="small text-secondary">${appointment.treatment}</span>
                ${getStatusBadge(appointment.status)}
            </div>
        `;

        card.addEventListener('click', () => {
            if (window.AdminActionsModule) {
                window.AdminActionsModule.openAppointmentDetail(appointment.id);
            }
        });

        return card;
    }

    function getStatusClass(status) {
        const map = {
            'Confirmada': 'confirmed',
            'Por confirmar': 'pending',
            'Atendida': 'confirmed',
            'Cancelada': 'noshow'
        };
        return map[status] || 'pending';
    }

    function getStatusBadge(status) {
        const map = {
            'Confirmada': ['success', 'Confirmada'],
            'Por confirmar': ['warning', 'Pendiente'],
            'Atendida': ['info', 'Atendida'],
            'Cancelada': ['danger', 'Cancelada']
        };
        const [variant, label] = map[status] || ['secondary', status];
        return `<span class="badge text-bg-${variant} badge-state">${label}</span>`;
    }

    function showLoading(show) {
        if (elements.loadingState) {
            elements.loadingState.classList.toggle('d-none', !show);
        }
        if (elements.fiveGrid) {
            elements.fiveGrid.style.opacity = show ? '0.5' : '1';
        }
        if (elements.mobileList) {
            elements.mobileList.style.opacity = show ? '0.5' : '1';
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

    // Public API
    return {
        init: init,
        reload: loadAppointments,
        getAppointments: () => appointments
    };
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', AdminCalendarModule.init);
} else {
    AdminCalendarModule.init();
}

// Make module available globally
window.AdminCalendarModule = AdminCalendarModule;
