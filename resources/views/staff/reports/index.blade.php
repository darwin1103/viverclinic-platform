@extends('layouts.employee')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Mis Reportes</h4>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-graph-up me-2"></i>Productividad</span>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 300px;">
                    <h1 class="display-1 fw-bold text-success mb-0">{{ $currentMonthAppointments }}</h1>
                    <p class="text-muted fw-semibold fs-5 mt-2">Citas este mes</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-plus me-2"></i>Nuevos Pacientes (30 días)</span>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 300px;">
                    <h1 class="display-1 fw-bold text-primary mb-0">{{ $newPatientsLast30Days }}</h1>
                    <p class="text-muted fw-semibold fs-5 mt-2">Pacientes registrados</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
