document.addEventListener('DOMContentLoaded', function() {
    const radios = document.getElementsByName('payment_method');
    const transferDetails = document.getElementById('transfer-details');
    const btnText = document.getElementById('btn-text');

    const updateUI = () => {
        // Obtener valor seleccionado
        let selected = 'GATEWAY';
        for (const radio of radios) {
            if (radio.checked) {
                selected = radio.value;
                break;
            }
        }

        // Mostrar/Ocultar upload de archivo
        if (selected === 'TRANSFER') {
            transferDetails.classList.remove('d-none');
            btnText.textContent = "Enviar Comprobante";
        } else if (selected === 'CASH') {
            transferDetails.classList.add('d-none');
            btnText.textContent = "Confirmar Pedido";
        } else {
            transferDetails.classList.add('d-none');
            btnText.textContent = "Pagar Ahora";
        }
    };

    // Listeners
    radios.forEach(radio => radio.addEventListener('change', updateUI));

    // Init
    updateUI();
});
