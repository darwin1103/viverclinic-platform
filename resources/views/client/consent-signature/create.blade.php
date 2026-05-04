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
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Consentimiento del Tratamiento</label>
                            <div class="p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto;">
                                {!! nl2br(e($contractedTreatment->treatment->informed_consent)) !!}
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('terms_accepted') is-invalid @enderror" type="checkbox" value="1" id="terms_accepted" name="terms_accepted" required>
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
@endsection
