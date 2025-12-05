// Admin Filters Module
const AdminFiltersModule = (function() {
    let currentFilters = {
        staff_id: '',
        treatment_id: '',
        status: '',
        search: '',
        branch_id: '',
    };

    let searchTimeout = null;

    const elements = {
        filterStaff: document.getElementById('filterStaff'),
        filterStaffMobile: document.getElementById('filterStaffMobile'),
        filterTreatment: document.getElementById('filterTreatment'),
        filterTreatmentMobile: document.getElementById('filterTreatmentMobile'),
        filterStatus: document.getElementById('filterStatus'),
        filterStatusMobile: document.getElementById('filterStatusMobile'),
        searchInput: document.getElementById('searchInput'),
        searchInputMobile: document.getElementById('searchInputMobile'),
        btnClearFilters: document.getElementById('btnClearFilters'),
        btnClearFiltersMobile: document.getElementById('btnClearFiltersMobile'),
        btnOpenRange: document.getElementById('btnOpenRange'),
        btnOpenRangeMobile: document.getElementById('btnOpenRangeMobile'),
        modalRange: document.getElementById('modalRange'),
        rangeMonthLabel: document.getElementById('rangeMonthLabel'),
        rangeDays: document.getElementById('rangeDays'),
        rangePreview: document.getElementById('rangePreview'),
        rangeApply: document.getElementById('rangeApply'),
        rangePrevMonth: document.getElementById('rangePrevMonth'),
        rangeNextMonth: document.getElementById('rangeNextMonth'),
        branchSelector: document.getElementById('branch-selector'),
    };

    let rangeMonthRef = new Date();
    let rangeSelected = null;

    function init() {
        attachEventListeners();
        loadFilterOptions();
    }

    function attachEventListeners() {
        // Staff filters
        if (elements.filterStaff) {
            elements.filterStaff.addEventListener('change', (e) => {
                currentFilters.staff_id = e.target.value;
                if (elements.filterStaffMobile) elements.filterStaffMobile.value = e.target.value;
                applyFilters();
            });
        }
        if (elements.filterStaffMobile) {
            elements.filterStaffMobile.addEventListener('change', (e) => {
                currentFilters.staff_id = e.target.value;
                if (elements.filterStaff) elements.filterStaff.value = e.target.value;
                applyFilters();
            });
        }

        // Treatment filters
        if (elements.filterTreatment) {
            elements.filterTreatment.addEventListener('change', (e) => {
                currentFilters.treatment_id = e.target.value;
                if (elements.filterTreatmentMobile) elements.filterTreatmentMobile.value = e.target.value;
                applyFilters();
            });
        }
        if (elements.filterTreatmentMobile) {
            elements.filterTreatmentMobile.addEventListener('change', (e) => {
                currentFilters.treatment_id = e.target.value;
                if (elements.filterTreatment) elements.filterTreatment.value = e.target.value;
                applyFilters();
            });
        }

        // Status filters
        if (elements.filterStatus) {
            elements.filterStatus.addEventListener('change', (e) => {
                currentFilters.status = e.target.value;
                if (elements.filterStatusMobile) elements.filterStatusMobile.value = e.target.value;
                applyFilters();
            });
        }
        if (elements.filterStatusMobile) {
            elements.filterStatusMobile.addEventListener('change', (e) => {
                currentFilters.status = e.target.value;
                if (elements.filterStatus) elements.filterStatus.value = e.target.value;
                applyFilters();
            });
        }

        // Search inputs
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', (e) => {
                currentFilters.search = e.target.value;
                if (elements.searchInputMobile) elements.searchInputMobile.value = e.target.value;
                debounceSearch();
            });
        }
        if (elements.searchInputMobile) {
            elements.searchInputMobile.addEventListener('input', (e) => {
                currentFilters.search = e.target.value;
                if (elements.searchInput) elements.searchInput.value = e.target.value;
                debounceSearch();
            });
        }

        if (elements.branchSelector) {
            elements.branchSelector.addEventListener('input', (e) => {
                currentFilters.branch_id = e.target.value;
                if (elements.branchSelector) elements.branchSelector.value = e.target.value;
                debounceSearch();
            });
        }

        // Clear filters
        if (elements.btnClearFilters) {
            elements.btnClearFilters.addEventListener('click', clearFilters);
        }
        if (elements.btnClearFiltersMobile) {
            elements.btnClearFiltersMobile.addEventListener('click', clearFilters);
        }

        // Range selector
        if (elements.btnOpenRange) {
            elements.btnOpenRange.addEventListener('click', openRangeSelector);
        }
        if (elements.btnOpenRangeMobile) {
            elements.btnOpenRangeMobile.addEventListener('click', openRangeSelector);
        }
        if (elements.rangePrevMonth) {
            elements.rangePrevMonth.addEventListener('click', () => navigateRangeMonth(-1));
        }
        if (elements.rangeNextMonth) {
            elements.rangeNextMonth.addEventListener('click', () => navigateRangeMonth(1));
        }
        if (elements.rangeApply) {
            elements.rangeApply.addEventListener('click', applyRange);
        }
    }

    async function loadFilterOptions() {
        await loadStaffList();
        await loadTreatmentsList();
    }

    async function loadStaffList() {
        try {
            const response = await fetch(window.apiEndpoints.getStaffList);
            const data = await response.json();
            const staff = data.staff || [];

            const options = staff.map(s => `<option value="${s.id}">${s.name}</option>`).join('');

            if (elements.filterStaff) {
                elements.filterStaff.innerHTML = '<option value="">Todos los profesionales</option>' + options;
            }
            if (elements.filterStaffMobile) {
                elements.filterStaffMobile.innerHTML = '<option value="">Todos los profesionales</option>' + options;
            }
        } catch (error) {
            console.error('Error loading staff list:', error);
        }
    }

    async function loadTreatmentsList() {
        try {
            const response = await fetch(window.apiEndpoints.getTreatmentsList);
            const data = await response.json();
            const treatments = data.treatments || [];

            const options = treatments.map(t => `<option value="${t.id}">${t.name}</option>`).join('');

            if (elements.filterTreatment) {
                elements.filterTreatment.innerHTML = '<option value="">Todos los tratamientos</option>' + options;
            }
            if (elements.filterTreatmentMobile) {
                elements.filterTreatmentMobile.innerHTML = '<option value="">Todos los tratamientos</option>' + options;
            }
        } catch (error) {
            console.error('Error loading treatments list:', error);
        }
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    }

    function applyFilters() {
        if (window.AdminCalendarModule) {
            window.AdminCalendarModule.reload();
        }
    }

    function clearFilters() {
        currentFilters = {
            staff_id: '',
            treatment_id: '',
            status: '',
            search: '',
            branch_id: '',
        };

        // Clear desktop filters
        if (elements.filterStaff) elements.filterStaff.value = '';
        if (elements.filterTreatment) elements.filterTreatment.value = '';
        if (elements.filterStatus) elements.filterStatus.value = '';
        if (elements.searchInput) elements.searchInput.value = '';

        // Clear mobile filters
        if (elements.filterStaffMobile) elements.filterStaffMobile.value = '';
        if (elements.filterTreatmentMobile) elements.filterTreatmentMobile.value = '';
        if (elements.filterStatusMobile) elements.filterStatusMobile.value = '';
        if (elements.searchInputMobile) elements.searchInputMobile.value = '';

        applyFilters();
    }

    function getFilters() {
        return { ...currentFilters };
    }

    // Range Selector Functions
    function openRangeSelector() {
        rangeSelected = null;
        rangeMonthRef = new Date();

        if (elements.rangePreview) {
            elements.rangePreview.textContent = '—';
        }
        if (elements.rangeApply) {
            elements.rangeApply.disabled = true;
        }

        renderRangeCalendar();

        const modal = new bootstrap.Modal(elements.modalRange);
        modal.show();
    }

    function navigateRangeMonth(direction) {
        rangeMonthRef.setMonth(rangeMonthRef.getMonth() + direction);
        renderRangeCalendar();
    }

    function renderRangeCalendar() {
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        if (elements.rangeMonthLabel) {
            elements.rangeMonthLabel.textContent = `${monthNames[rangeMonthRef.getMonth()]} ${rangeMonthRef.getFullYear()}`;
        }

        if (!elements.rangeDays) return;

        elements.rangeDays.innerHTML = '';

        const firstDay = new Date(rangeMonthRef.getFullYear(), rangeMonthRef.getMonth(), 1);
        const startDay = (firstDay.getDay() + 6) % 7;
        const daysInMonth = new Date(rangeMonthRef.getFullYear(), rangeMonthRef.getMonth() + 1, 0).getDate();

        // Empty cells
        for (let i = 0; i < startDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-cell cell-muted';
            empty.style.visibility = 'hidden';
            elements.rangeDays.appendChild(empty);
        }

        // Days
        for (let d = 1; d <= daysInMonth; d++) {
            const date = new Date(rangeMonthRef.getFullYear(), rangeMonthRef.getMonth(), d);
            const cell = document.createElement('div');
            cell.className = 'calendar-cell';
            cell.textContent = d;

            if (rangeSelected && sameYMD(date, rangeSelected)) {
                cell.classList.add('cell-selected');
            }

            cell.addEventListener('click', () => selectRangeDate(date));
            elements.rangeDays.appendChild(cell);
        }
    }

    function selectRangeDate(date) {
        rangeSelected = date;

        const end = new Date(date);
        end.setDate(end.getDate() + 4);

        const startTxt = formatHuman(date, { day: '2-digit', month: 'short' }).replace('.', '');
        const endTxt = formatHuman(end, { day: '2-digit', month: 'short' }).replace('.', '');

        if (elements.rangePreview) {
            elements.rangePreview.textContent = `${startTxt} – ${endTxt}`;
        }

        if (elements.rangeApply) {
            elements.rangeApply.disabled = false;
        }

        renderRangeCalendar();
    }

    function applyRange() {
        if (!rangeSelected) return;

        // Update the calendar module's current start date
        // This is a bit hacky but works for the demo
        if (window.AdminCalendarModule) {
            // Force update the calendar's currentStart
            // You may need to expose this method in the calendar module
            window.location.reload(); // Simple reload for now
        }

        const modal = bootstrap.Modal.getInstance(elements.modalRange);
        if (modal) modal.hide();
    }

    function sameYMD(date1, date2) {
        return date1 && date2 &&
            date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate();
    }

    function formatHuman(date, options = {}) {
        return date.toLocaleDateString('es-ES', options);
    }

    return {
        init: init,
        getFilters: getFilters,
        clearFilters: clearFilters
    };
})();

// Initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', AdminFiltersModule.init);
} else {
    AdminFiltersModule.init();
}

window.AdminFiltersModule = AdminFiltersModule;


