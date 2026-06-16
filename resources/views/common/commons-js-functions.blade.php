@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        iziToast.settings({
            close: true,
            timeout: 5000,
            progressBar: false,
            resetOnHover: true,
            position: 'bottomLeft',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            transitionInMobile: 'fadeInUp',
            transitionOutMobile: 'fadeOutDown',
            onOpening: function() {},
            onOpened: function() {},
            onClosing: function() {},
            onClosed: function() {}
        });
        @if (Session::has('success'))
            iziToast.success({
                message: '{{ __(Session::get('success')) }}'
            });
        @elseif (Session::has('info'))
            iziToast.info({
                message: '{{ __(Session::get('info')) }}'
            });
        @elseif (Session::has('warning'))
            iziToast.warning({
                message: '{{ __(Session::get('warning')) }}'
            });
        @elseif (Session::has('error'))
            iziToast.error({
                message: '{{ __(Session::get('error')) }}'
            });
        @endif
    }, false);
    function ajaxErrorHandle(error) {
        if (error.status == 401 || error.status == 419) {
            bootstrap.Modal.getOrCreateInstance('#loginModal').show();
        } else if(error.status == 422) {
            let errorMsg = '';
            if (error.responseJSON && error.responseJSON.errors) {
                const errors = error.responseJSON.errors;
                for (const key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        errorMsg += errors[key][0] + '<br>';
                    }
                }
            } else if (error.responseJSON && error.responseJSON.message) {
                errorMsg = error.responseJSON.message;
            } else {
                errorMsg = "{{ __('Validation error') }}";
            }
            iziToast.warning({
                message: errorMsg
            });
        } else if(error.status == 409) {
            iziToast.info({
                message: "{{ __('Restricted operation, please notify to administrator') }}"
            });
        } else if(error.status == 403) {
            iziToast.info({
                message: "{{ __('Operation not allowed') }}"
            });
        } else {
            let serverMsg = '';
            if (error.responseJSON && error.responseJSON.error) {
                serverMsg = error.responseJSON.error;
            } else if (error.responseJSON && error.responseJSON.message) {
                serverMsg = error.responseJSON.message;
            }
            
            if (serverMsg) {
                iziToast.error({
                    message: serverMsg
                });
            } else {
                iziToast.error({
                    message: "{{ __('Something went wrong, please try again, if the problem persists, please report it to administrator') }}"
                });
            }
        }
    }

    // --- Currency Formatting Logic ---
    document.addEventListener('DOMContentLoaded', function () {
        const currencyInputs = document.querySelectorAll('.currency-input');

        function formatCurrencyValue(value) {
            // Strip everything except digits
            let cleanValue = value.replace(/\D/g, "");
            if (cleanValue === "") {
                return "";
            }
            // Format with dots for thousands
            return cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        currencyInputs.forEach(input => {
            // Format initially on load
            let initialValue = String(input.value);
            // If the initial value has a decimal point (from database decimal fields like .00), remove it
            // before stripping non-digits, otherwise "119000.00" becomes "11900000".
            if (initialValue.includes('.')) {
                initialValue = initialValue.split('.')[0];
            }
            input.value = formatCurrencyValue(initialValue);
        });

        // Use event delegation for format on input to support dynamically added fields
        document.addEventListener('input', function (e) {
            if (e.target && e.target.classList.contains('currency-input')) {
                let formatted = formatCurrencyValue(e.target.value);
                if (e.target.value !== formatted) {
                    e.target.value = formatted;
                }
            }
        });

        // Strip formatting before form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                form.querySelectorAll('.currency-input').forEach(input => {
                    input.value = input.value.replace(/\./g, '');
                });
            });
        });
    });
</script>
@endpush
