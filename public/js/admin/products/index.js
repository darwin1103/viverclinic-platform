document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const headerBranchSelect = document.getElementById('branch-selector'); // El select del header
    const hiddenBranchFilter = document.getElementById('branch_id_filter'); // El input hidden del formulario
    const tableContainer = document.getElementById('products-table-container');
    const filterForm = document.getElementById('filter-form');

    let debounceTimer;

    // Función para realizar la petición fetch
    const fetchProducts = () => {
        // Construir la URL con los parámetros
        const url = new URL(filterForm.action);
        const params = new URLSearchParams();

        if (searchInput.value) {
            params.append('search', searchInput.value);
        }

        if (hiddenBranchFilter.value) {
            params.append('branch_id', hiddenBranchFilter.value);
        }

        // Marcar que es una petición AJAX
        url.search = params.toString();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            attachDeleteListeners(); // Reasignar eventos a los botones nuevos
        })
        .catch(error => console.error('Error cargando productos:', error));
    };

    // 1. Lógica del Buscador con Debounce
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchProducts();
            }, 500); // 500ms debounce
        });

        // Prevenir envío del form con Enter
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(debounceTimer);
                fetchProducts();
            }
        });
    }

    // 2. Lógica del Selector de Sucursal (Sincronización con Header)
    if (headerBranchSelect) {
        // Sincronizar valor inicial al cargar la página
        hiddenBranchFilter.value = headerBranchSelect.value;

        // Escuchar cambios en el header
        headerBranchSelect.addEventListener('change', function () {
            hiddenBranchFilter.value = this.value;
            fetchProducts();
        });
    }

    // 3. Lógica de Eliminación (Modal Confirmación Nativo)
    const attachDeleteListeners = () => {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');

                if (confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
                    form.submit();
                }
            });
        });
    };

    // Inicializar listeners al cargar
    attachDeleteListeners();
    fetchProducts();
});
