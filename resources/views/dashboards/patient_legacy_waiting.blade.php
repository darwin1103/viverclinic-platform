@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <img src="{{ asset('images/logo-viverclinic.png') }}" alt="ViverClinic Logo" class="img-fluid mb-4" style="max-width: 200px; opacity: 0.8;">
                    
                    <h2 class="fw-bold mb-3" style="color: var(--primary-color);">¡Bienvenido a la nueva plataforma de ViverClinic!</h2>
                    
                    <div class="alert alert-info rounded-3 text-start mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Tu cuenta está en revisión.</strong>
                    </div>

                    <p class="fs-5 text-muted mb-4">
                        Hemos registrado tu historia clínica exitosamente. Como eres un usuario antiguo, estamos validando tus registros previos para asignarte tus tratamientos y sesiones restantes en este nuevo sistema.
                    </p>
                    
                    <p class="text-secondary">
                        Este proceso puede tardar unos minutos. Por favor, espera a que un administrador configure tu cuenta. 
                        Te notificaremos o puedes refrescar esta página más tarde.
                    </p>
                    
                    <hr class="my-4">
                    
                    <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4 py-2 mt-2">
                        <i class="bi bi-arrow-clockwise me-2"></i> Refrescar estado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
