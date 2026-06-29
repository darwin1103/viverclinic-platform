@extends('layouts.admin')

@section('content')
<div class="container-fluid p-0 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold mb-1">
                <a href="{{ route('admin.reports.index', ['from' => request('from'), 'to' => request('to'), 'branch_id' => request('branch_id')]) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-2"></i>Reportes
                </a> 
                / Rendimiento de Staff
            </h1>
            <p class="text-muted mb-0">Citas completadas por <strong>{{ $user->name }}</strong> entre el {{ $from->format('d/m/Y') }} y el {{ $to->format('d/m/Y') }}</p>
        </div>
        
        <div>
            <form action="{{ route('admin.reports.staff-detail', $user->id) }}" method="GET" class="d-flex gap-2">
                @if(request('branch_id'))
                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark text-white border-secondary">Desde</span>
                    <input type="date" name="from" class="form-control bg-dark text-white border-secondary" value="{{ $from->format('Y-m-d') }}" required>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark text-white border-secondary">Hasta</span>
                    <input type="date" name="to" class="form-control bg-dark text-white border-secondary" value="{{ $to->format('Y-m-d') }}" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3">Filtrar</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Paciente</th>
                            <th>Tratamiento</th>
                            <th class="text-center">Calificación</th>
                            <th>Comentario</th>
                            <th class="text-end pe-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($appointment->schedule)->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($appointment->schedule)->format('h:i A') }}</div>
                                </td>
                                <td>
                                    @if($appointment->contractedTreatment && $appointment->contractedTreatment->user)
                                        {{ $appointment->contractedTreatment->user->name }}
                                    @else
                                        <span class="text-muted">Desconocido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appointment->contractedTreatment && $appointment->contractedTreatment->treatment)
                                        <span class="badge bg-secondary">{{ $appointment->contractedTreatment->treatment->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($appointment->review_score)
                                        <span class="text-warning">
                                            @for($i = 1; $i <= 3; $i++)
                                                <i class="bi bi-star{{ $i <= $appointment->review_score ? '-fill' : '' }}"></i>
                                            @endfor
                                        </span>
                                    @else
                                        <span class="text-muted small">Sin calificar</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appointment->review)
                                        <span class="text-muted fst-italic">"{{ Str::limit($appointment->review, 60) }}"</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-{{ $appointment->status == 'Completada' ? 'success' : 'info' }}">
                                        {{ $appointment->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                    No hay citas completadas en este periodo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($appointments->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                    {{ $appointments->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
