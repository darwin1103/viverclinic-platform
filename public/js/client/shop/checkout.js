document.addEventListener('DOMContentLoaded', function() {
    const radios = document.getElementsByName('payment_method');
    const transferDetails = document.getElementById('transfer-details');
    const btnText = document.getElementById('btn-text');

    // Contenedores de botones
    const standardContainer = document.getElementById('standard-submit-container');
    const wompiContainer = document.getElementById('wompi-widget-container');

    const updateUI = () => {
        // Obtener valor seleccionado
        let selected = null;
        for (const radio of radios) {
            if (radio.checked) {
                selected = radio.value;
                break;
            }
        }

        // Reset UI
        if (transferDetails) transferDetails.classList.add('d-none');

        // Lógica de visualización de botones
        if (selected === 'GATEWAY') {
            // Ocultar form estándar, mostrar Wompi
            if(standardContainer) standardContainer.classList.add('d-none');
            if(wompiContainer) wompiContainer.classList.remove('d-none');
        } else {
            // Mostrar form estándar, ocultar Wompi
            if(standardContainer) standardContainer.classList.remove('d-none');
            if(wompiContainer) wompiContainer.classList.add('d-none');

            // Textos y detalles
            if (selected === 'TRANSFER') {
                if(transferDetails) transferDetails.classList.remove('d-none');
                btnText.textContent = "Enviar Comprobante";
            } else {
                btnText.textContent = "Confirmar Pedido";
            }
        }
    };

    // Listeners
    radios.forEach(radio => radio.addEventListener('change', updateUI));

    // Init
    updateUI();
});
