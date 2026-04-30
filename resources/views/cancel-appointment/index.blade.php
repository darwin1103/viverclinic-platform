@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="">
        <h1 class="h3">Cancelar Cita</h1>

        <div class="card my-4 shadow-sm">
            <div class="card-body">
                @if($nextAppointment)
                    <div class="text-center mb-4">
                        <i class="bi bi-calendar-x text-danger display-1"></i>
                        <h4 class="mt-3">¿Estás seguro de que deseas cancelar tu cita?</h4>
                        <p class="text-muted">La sesión será devuelta a tu paquete de tratamientos.</p>
                    </div>

                    <div class="card bg-dark border border-secondary mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-white border-bottom border-secondary pb-2 mb-3">
                                Detalles de la Cita
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Tratamiento</small>
                                    <span class="fs-5 fw-medium text-white">{{ $nextAppointment->contractedTreatment->treatment->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Sucursal</small>
                                    <span class="fs-5 fw-medium text-white">{{ $nextAppointment->contractedTreatment->branch->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Fecha</small>
                                    <span class="fs-5 fw-medium text-white">{{ \Carbon\Carbon::parse($nextAppointment->schedule)->isoFormat('D MMMM YYYY') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Hora</small>
                                    <span class="fs-5 fw-medium text-white">{{ \Carbon\Carbon::parse($nextAppointment->schedule)->isoFormat('h:mm a') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light px-4">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                        <form method="POST" action="{{ route('cancel-appointment.destroy', $nextAppointment->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-x-circle me-2"></i>Sí, cancelar cita
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-check text-muted display-1"></i>
                        <h4 class="mt-4 text-muted">No tienes citas próximas</h4>
                        <p class="text-secondary mb-4">No se encontraron citas agendadas o pendientes para cancelar.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light px-4">
                            <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
