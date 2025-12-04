document.addEventListener('DOMContentLoaded', function() {
    // Elementos del Modal
    const stockModalEl = document.getElementById('stockModal');
    const stockModal = new bootstrap.Modal(stockModalEl);
    const stockForm = document.getElementById('stock-form');
    const opSwitch = document.getElementById('operationSwitch');
    const opLabel = document.getElementById('operationLabel');
    const opValue = document.getElementById('operationValue');

    // Botón que abre el modal
    const btnStock = document.querySelector('.btn-stock-modal');
    const displayStockInput = document.getElementById('display-stock');

    // Evento Abrir Modal
    if (btnStock) {
        btnStock.addEventListener('click', function() {
            // Llenar datos en el modal
            document.getElementById('modal-asset-id').value = this.dataset.id;
            document.getElementById('modal-asset-name').textContent = this.dataset.name;
            document.getElementById('modal-current-stock').textContent = this.dataset.stock;

            // Resetear form y switch
            stockForm.reset();
            opSwitch.checked = true;
            updateSwitchState();

            stockModal.show();
        });
    }

    // Lógica del Switch (Agregar/Eliminar)
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

    // Enviar Formulario
    stockForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const assetId = document.getElementById('modal-asset-id').value;
        const formData = new FormData(stockForm);
        const data = Object.fromEntries(formData.entries());

        // Calculamos nuevo stock para actualizar la vista visualmente
        const quantity = parseInt(data.quantity);
        const currentStock = parseInt(btnStock.dataset.stock);
        const isAddition = data.operation === 'add';

        // Validación frontend básica antes de enviar
        if(!isAddition && (currentStock - quantity) < 0) {
            alert('El stock no puede ser menor a cero.');
            return;
        }

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
            if (!response.ok) throw new Error(res.message || 'Error desconocido');
            return res;
        })
        .then(resData => {
            stockModal.hide();
            alert('Stock actualizado correctamente.');
            location.reload();
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });
});

function openEditNoteModal(id, content) {
    const modalEl = document.getElementById('editNoteModal');
    const form = document.getElementById('editNoteForm');
    const contentArea = document.getElementById('editNoteContent');

    // Construir la ruta dinámicamente
    form.action = `/admin/assets/notes/${id}`;
    contentArea.value = content;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
