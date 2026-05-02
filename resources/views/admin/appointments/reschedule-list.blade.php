@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Reagendar Citas</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-x me-2 text-secondary"></i>Citas no asistidas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha Original</th>
                        <th>Paciente</th>
                        <th>Tratamiento</th>
                        <th>Profesional</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center fw-semibold py-3 text-muted">Aún no hay citas para reagendar</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
