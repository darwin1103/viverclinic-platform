<div class="modal fade" id="removeConfirmationModal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="removeConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="removeConfirmationModalLabel">{{__('Delete')}}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <p class="fs-6 fw-normal">
                            {{ __("Are you sure? This action is permanent and cannot be undone") }}.
                        </p>
                    </div>
                    <div class="d-grid gap-2 d-md-block text-end">
                        <a class="btn btn-danger" id="deleteElementBtn" role="button"
                            onclick="event.preventDefault(); document.getElementById('delete').submit();">
                            {{ __('Yes, delete it') }}
                        </a>
                        <form id="delete" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>