@extends('layouts.employee')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Paquetes Disponibles</h4>
    </div>

    <div class="row g-3 mt-3">
        @forelse($packages as $package)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <span class="badge @if($package->status == 'Activo') bg-success @else bg-secondary @endif">
                            {{ $package->status }}
                        </span>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $package->created_at->format('d/m/Y') }}</small>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-white mb-1">{{ $package->treatment->name ?? 'Tratamiento sin nombre' }}</h5>
                        <p class="card-text text-muted mb-3"><i class="bi bi-person me-2"></i>{{ $package->user->name ?? 'Paciente Desconocido' }}</p>
                        
                        <div class="d-flex align-items-center justify-content-between mt-3 p-2 bg-dark rounded">
                            <span class="text-secondary small">Total Sesiones</span>
                            <span class="fw-bold text-white">{{ $package->sessions }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="card bg-transparent border-0">
                    <div class="card-body">
                        <i class="bi bi-bag-x display-4 text-muted mb-3 d-block"></i>
                        <p class="text-muted fw-semibold mb-0">Aún no hay paquetes asignados o disponibles</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

@endsection
