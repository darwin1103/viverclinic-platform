document.addEventListener('DOMContentLoaded', function () {

    // --- Debounce Function ---
    /**
     * Creates a debounced version of a function that delays its execution.
     * @param {Function} func The function to debounce.
     * @param {number} delay The delay in milliseconds.
     * @returns {Function} The debounced function.
     */
    const debounce = (func, delay = 500) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    };

    // --- Filter Logic ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const treatmentSelect = document.getElementById('treatment_id');
    const minDateInput = document.getElementById('min_date');
    const maxDateInput = document.getElementById('max_date');

    /**
     * Submits the filter form.
     */
    const submitFilterForm = () => {
        filterForm.submit();
    };

    // Create a debounced version of the form submission
    const debouncedSubmit = debounce(submitFilterForm);

    if (filterForm) {
        // Apply debounce to text and date inputs to avoid too many requests
        [searchInput, minDateInput, maxDateInput].forEach(input => {
            if (input) {
                input.addEventListener('input', debouncedSubmit);
            }
        });

        // Submit immediately on select change
        if (treatmentSelect) {
            treatmentSelect.addEventListener('change', submitFilterForm);
        }
    }


    // --- Modal Logic ---
    const appointmentModal = document.getElementById('appointmentActionModal');
    if (appointmentModal) {
        appointmentModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;

            // Extract info from data-* attributes
            const appointmentId = button.getAttribute('data-appointment-id');
            const patientName = button.getAttribute('data-patient-name');

            // Update the modal's content.
            const modalTitle = appointmentModal.querySelector('.modal-title');
            const modalAppointmentIdElement = appointmentModal.querySelector('#modalAppointmentId');

            modalTitle.textContent = `Detalles de la Cita de: ${patientName}`;

            if(modalAppointmentIdElement) {
                modalAppointmentIdElement.textContent = appointmentId;
            }

            // Here you would typically make an AJAX call (e.g., using fetch)
            // to get the appointment details and populate your form inside the modal body.
            // Example:
            // fetch(`/staff/appointment/${appointmentId}/details`)
            //     .then(response => response.json())
            //     .then(data => {
            //         // Populate form fields with 'data'
            //     });
        });
    }
});
