document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Lógica de filtros
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search-input');
    const branchSelector = document.getElementById('branch-selector');
    const branchIdFilterInput = document.getElementById('branch-id-filter');

    // Función Debounce para no saturar el servidor con peticiones
    const debounce = (callback, wait) => {
        let timeoutId = null;
        return (...args) => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(() => {
                callback.apply(null, args);
            }, wait);
        };
    }

    // Función para enviar el formulario de filtros
    const submitFilterForm = () => {
        filterForm.submit();
    }

    // Envoltura Debounce de la función de envío
    const debouncedSubmit = debounce(submitFilterForm, 500); // 500ms de espera

    // Event listener para el campo de búsqueda (con debounce)
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSubmit);
    }

    // Event listener para el selector de sucursal (envío inmediato)
    if (branchSelector) {
        branchSelector.addEventListener('change', (event) => {
            if (branchIdFilterInput) {
                branchIdFilterInput.value = event.target.value;
            }
            submitFilterForm();
        });
    }

});
