document.addEventListener('DOMContentLoaded', function () {
    // --- DEBOUNCE FUNCTION ---
    // A function to delay execution of a callback
    const debounce = (callback, wait) => {
        let timeoutId = null;
        return (...args) => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(() => {
                callback.apply(null, args);
            }, wait);
        };
    };

    // --- DOM ELEMENTS ---
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const treatmentSelect = document.getElementById('treatment_id_filter');
    const packageSelect = document.getElementById('package_id_filter');
    const branchSelect = document.getElementById('branch-selector'); // External selector from header
    const branchHiddenInput = document.getElementById('branch_id_hidden');

    // --- FORM SUBMISSION LOGIC ---
    const submitForm = () => {
        filterForm.submit();
    };

    // Create a debounced version of the submit function
    const debouncedSubmit = debounce(submitForm, 400); // 400ms delay

    // --- EVENT LISTENERS ---

    // 1. Search input
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSubmit);
    }

    // 2. Treatment select
    if (treatmentSelect) {
        treatmentSelect.addEventListener('change', submitForm);
    }

    // 3. Package select
    if (packageSelect) {
        packageSelect.addEventListener('change', submitForm);
    }

    // 4. Branch selector (from header)
    if (branchSelect && branchHiddenInput) {
        branchSelect.addEventListener('change', () => {
            // Update the hidden input value with the selected branch
            branchHiddenInput.value = branchSelect.value;
            // Submit the form immediately
            submitForm();
        });
    }

    // Prevent form submission on pressing Enter in the search field
    filterForm.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
});
