document.addEventListener('DOMContentLoaded', function() {
    const mainImageInput = document.getElementById('main_image');
    const imagePreview = document.getElementById('imagePreview');

    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }

    // Usamos delegación de eventos para manejar clics en botones que aún no existen
    document.body.addEventListener('click', function(event) {
        // Botón para agregar un nuevo paquete
        if (event.target.classList.contains('add-package-btn')) {
            const branchId = event.target.dataset.branchId;
            const container = document.getElementById(`packages-container-${branchId}`);
            const newIndex = Date.now(); // Usamos timestamp para un índice único
            const packageFormHtml = getPackageFormTemplate(branchId, newIndex);

            // Insertamos el nuevo formulario en el contenedor
            container.insertAdjacentHTML('beforeend', packageFormHtml);
        }

        // Botón para eliminar un paquete
        if (event.target.closest('.remove-package-btn')) {
            event.preventDefault();
            // Buscamos el contenedor del paquete y lo eliminamos
            event.target.closest('.package-form-row').remove();
        }
    });

    function getPackageFormTemplate(branchId, index, packageData = {}) {
        const name = packageData.name || '';
        const price = packageData.price || '';
        const big_zones = packageData.big_zones || '';
        const mini_zones = packageData.mini_zones || '';

        return `
            <div class="row g-3 p-3 border rounded mb-3 package-form-row">
                <div class="col-12 col-md-3">
                    <label class="form-label">Nombre del paquete</label>
                    <input type="text" name="branches[${branchId}][packages][${index}][name]" class="form-control" placeholder="Ej: Paquete Premium" value="${name}" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Precio</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="branches[${branchId}][packages][${index}][price]" class="form-control" placeholder="0.00" step="0.01" min="0" value="${price}" required>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Zonas Grandes</label>
                    <input type="number" name="branches[${branchId}][packages][${index}][big_zones]" class="form-control" step="1" min="0" value="${big_zones}" required>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Mini Zonas</label>
                    <input type="number" name="branches[${branchId}][packages][${index}][mini_zones]" class="form-control" step="1" min="0" value="${mini_zones}" required>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger w-100 remove-package-btn">
                        <i class="bi bi-trash-fill"></i> Quitar
                    </button>
                </div>
            </div>
        `;
    }
});
