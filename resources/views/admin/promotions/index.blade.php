@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Promociones</h4>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Añadir nuevo
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-megaphone me-2"></i>Promociones Activas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descuento</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promotion)
                    <tr>
                        <td>{{ $promotion->title }}</td>
                        <td>{{ $promotion->discount }}%</td>
                        <td>{{ $promotion->start_date ? $promotion->start_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $promotion->end_date ? $promotion->end_date->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            @if($promotion->is_active)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-secondary">Inactiva</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" disabled>Editar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center fw-semibold py-3 text-muted">Aún no hay promociones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
