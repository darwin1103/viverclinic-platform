@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-file-earmark-medical me-2"></i> Consentimiento Informado</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Se te ha asignado el tratamiento <strong>{{ $contractedTreatment->treatment->name }}</strong>. 
                        Para poder agendar tus citas, es obligatorio que leas y aceptes los términos del consentimiento informado.
                    </div>

                    <form action="{{ route('client.consent-signature.store', $contractedTreatment->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input show-terms-conditions-modal @error('terms_accepted') is-invalid @enderror" type="checkbox" value="1" id="terms_accepted" name="terms_accepted" required>
                            <label class="form-check-label" for="terms_accepted">
                                He leído, entiendo y <strong>acepto los términos y condiciones</strong> descritos en este documento para el tratamiento {{ $contractedTreatment->treatment->name }}.
                            </label>
                            @error('terms_accepted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-person-standing-dress me-2"></i> Declaración de estado</h5>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('is_pregnant') is-invalid @enderror" type="radio" name="is_pregnant" id="pregnant_no" value="0" required>
                            <label class="form-check-label" for="pregnant_no">
                                Declaro que <strong>NO me encuentro en estado de embarazo</strong> en este momento.
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('is_pregnant') is-invalid @enderror" type="radio" name="is_pregnant" id="pregnant_yes" value="1" required>
                            <label class="form-check-label" for="pregnant_yes">
                                Me encuentro en estado de embarazo.
                            </label>
                            @error('is_pregnant')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="bi bi-pen me-2"></i> Firmar y Continuar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsConditionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="termsConditionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="termsConditionsModalLabel">Consentimiento Informado - {{ $contractedTreatment->treatment->name }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $contractedTreatment->treatment->terms_conditions !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="acceptTermsConditions">{{ __('I accept') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const termsCheck = document.querySelector(".show-terms-conditions-modal");
    if (termsCheck) {
        termsCheck.addEventListener('click', function(e) {
            if (this.checked) {
                // Prevenir check inmediato, mostrar modal primero
                e.preventDefault();
                const modalEl = document.getElementById('termsConditionsModal');
                if (modalEl) new bootstrap.Modal(modalEl).show();
            }
        });
    }

    const btnAcceptTerms = document.getElementById("acceptTermsConditions");
    if (btnAcceptTerms) {
        btnAcceptTerms.addEventListener('click', function() {
            const modalEl = document.getElementById('termsConditionsModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Marcar el checkbox ahora sí
            if (termsCheck) termsCheck.checked = true;
        });
    }
});
</script>
@endpush
