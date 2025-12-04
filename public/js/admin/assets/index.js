document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const branchFilterInput = document.getElementById('branch_id_filter');
    const headerBranchSelect = document.getElementById('branch-selector'); // El select del header
    const tableBody = document.getElementById('assets-table-body');
    const clearBtn = document.getElementById('btn-clear-filters');

    let debounceTimer;

    // 1. Sincronización con el select del header
    if (headerBranchSelect) {
        // Valor inicial
        branchFilterInput.value = headerBranchSelect.value;

        // Escuchar cambios en el header
        headerBranchSelect.addEventListener('change', function() {
            branchFilterInput.value = this.value;
            fetchAssets();
        });
    }

    // 2. Búsqueda con Debounce
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchAssets();
        }, 500); // Esperar 500ms antes de buscar
    });

    // 3. Botón Limpiar Filtros
    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        if (headerBranchSelect) {
            headerBranchSelect.value = '';
            // Disparar evento change manualmente para que actualice el input hidden y la tabla
            headerBranchSelect.dispatchEvent(new Event('change'));
        } else {
            branchFilterInput.value = '';
            fetchAssets();
        }
    });

    // 4. Función Principal para obtener datos (AJAX)
    function fetchAssets() {
        const search = searchInput.value;
        const branchId = branchFilterInput.value;

        // Construir URL
        const url = new URL(window.location.origin + '/admin/assets');
        if (search) url.searchParams.append('search', search);
        if (branchId) url.searchParams.append('branch_id', branchId);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableBody.innerHTML = html;
            attachDynamicEvents(); // Reasignar eventos a los nuevos botones
        })
        .catch(error => console.error('Error cargando activos:', error));
    }

    // 5. Asignar eventos a elementos dinámicos (creados tras el fetch)
    function attachDynamicEvents() {
        // Botones de Eliminar
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function () {
                const assetId = this.dataset.id;
                confirmDelete(assetId);
            });
        });

        // Botones de Stock
        document.querySelectorAll('.btn-stock-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                openStockModal(this.dataset);
            });
        });
    }

    // 6. Lógica de Eliminación (Usando confirm/alert nativos)
    function confirmDelete(id) {
        // Confirmación nativa
        if (confirm('¿Estás seguro?\nNo podrás revertir esto y se eliminarán las notas asociadas.')) {
            fetch(`/admin/assets/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.message); // Alerta nativa de éxito
                    fetchAssets();       // Recargar tabla
                } else {
                    alert('Error: No se pudo eliminar el activo.');
                }
            })
            .catch(error => {
                console.error(error);
                alert('Ocurrió un error inesperado al intentar eliminar.');
            });
        }
    }

    // --- LÓGICA DEL MODAL DE STOCK ---

    const stockModalEl = document.getElementById('stockModal');
    // Inicializamos el modal de Bootstrap
    const stockModal = new bootstrap.Modal(stockModalEl);
    const stockForm = document.getElementById('stock-form');
    const opSwitch = document.getElementById('operationSwitch');
    const opLabel = document.getElementById('operationLabel');
    const opValue = document.getElementById('operationValue');

    function openStockModal(data) {
        document.getElementById('modal-asset-id').value = data.id;
        document.getElementById('modal-asset-name').textContent = data.name;
        document.getElementById('modal-current-stock').textContent = data.stock;

        // Resetear formulario
        stockForm.reset();

        // Configurar estado inicial del switch (Agregar)
        opSwitch.checked = true;
        updateSwitchState();

        stockModal.show();
    }

    // Lógica visual del Switch (Agregar/Eliminar)
    opSwitch.addEventListener('change', updateSwitchState);

    function updateSwitchState() {
        if (opSwitch.checked) {
            opLabel.textContent = 'Agregar al inventario';
            opLabel.classList.remove('text-danger');
            opLabel.classList.add('text-success');
            opValue.value = 'add';
        } else {
            opLabel.textContent = 'Eliminar del inventario';
            opLabel.classList.remove('text-success');
            opLabel.classList.add('text-danger');
            opValue.value = 'remove';
        }
    }

    // Envio del formulario de Stock
    stockForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const assetId = document.getElementById('modal-asset-id').value;
        const formData = new FormData(stockForm);
        const data = Object.fromEntries(formData.entries());

        fetch(`/admin/assets/${assetId}/stock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            const res = await response.json();
            if (!response.ok) {
                // Si el backend devuelve error (ej: stock < 0), lanzamos excepción para el catch
                throw new Error(res.message || 'Error desconocido');
            }
            return res;
        })
        .then(data => {
            stockModal.hide();
            alert('Stock actualizado correctamente.\n' + data.message); // Alerta nativa
            fetchAssets(); // Refrescar tabla
        })
        .catch(error => {
            alert('Error: ' + error.message); // Alerta nativa de error
        });
    });

    // Inicializar eventos por primera vez
    attachDynamicEvents();
});
