document.addEventListener('DOMContentLoaded', function () {
    // Selectores
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const headerBranchSelect = document.getElementById('branch-selector'); // Select del Header existente
    const hiddenBranchFilter = document.getElementById('branch_id_filter'); // Input oculto
    const tableContainer = document.getElementById('orders-table-container');

    let debounceTimer;

    // Función principal de fetch
    const fetchOrders = () => {
        const url = new URL(filterForm.action);
        const params = new URLSearchParams();

        // Agregar parámetros si tienen valor
        if (searchInput.value) params.append('search', searchInput.value);
        if (dateFromInput.value) params.append('date_from', dateFromInput.value);
        if (dateToInput.value) params.append('date_to', dateToInput.value);
        if (hiddenBranchFilter.value) params.append('branch_id', hiddenBranchFilter.value);

        url.search = params.toString();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
        })
        .catch(error => console.error('Error cargando órdenes:', error));
    };

    // Handler para Debounce (Inputs de texto)
    const handleDebounce = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchOrders();
        }, 500);
    };

    // Eventos
    if (searchInput) {
        searchInput.addEventListener('input', handleDebounce);
    }

    // Para fechas, podemos usar debounce o change directo.
    // Usamos debounce por si el usuario escribe manualmente la fecha.
    if (dateFromInput) dateFromInput.addEventListener('input', handleDebounce);
    if (dateToInput) dateToInput.addEventListener('input', handleDebounce);

    // Lógica de Sucursal (Header)
    if (headerBranchSelect) {
        // Valor inicial
        hiddenBranchFilter.value = headerBranchSelect.value;

        // Al cambiar el select del header
        headerBranchSelect.addEventListener('change', function () {
            hiddenBranchFilter.value = this.value;
            fetchOrders();
        });
    }

    fetchOrders();

});
