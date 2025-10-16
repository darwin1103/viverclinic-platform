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
        } else if(error.status == 409) {
            iziToast.info({
                message: "{{ __('Restricted operation, please notify to administrator') }}"
            });
        } else if(error.status == 403) {
            iziToast.info({
                message: "{{ __('Operation not allowed') }}"
            });
        } else {
            iziToast.error({
                message: "{{ __('Something went wrong, please try again, if the problem persists, please report it to administrator') }}"
            });
        }
    }
</script>
@endpush
