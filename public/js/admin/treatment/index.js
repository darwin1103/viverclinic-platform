document.addEventListener('DOMContentLoaded', function() {
    const branchSelector = document.getElementById('branch-selector');
    const branchFilterInput = document.getElementById('branch_id_filter');
    const filterForm = document.getElementById('filter-form');

    // 1. Sincronizar el selector del header con el estado actual del filtro
    // Si la URL ya tiene un filtro de sucursal, seleccionamos esa opción en el header
    if (branchFilterInput.value) {
        branchSelector.value = branchFilterInput.value;
    }

    // 2. Escuchar cambios en el selector del header
    if (branchSelector) {
        branchSelector.addEventListener('change', function() {
            // Actualizar el valor del campo oculto en el formulario de filtros
            branchFilterInput.value = this.value;

            // Enviar el formulario automáticamente para aplicar el filtro
            filterForm.submit();
        });
    }
});
