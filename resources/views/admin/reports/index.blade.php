@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Reportes y Analíticas</h4>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-graph-up me-2"></i>Rendimiento Mensual</span>
                </div>
                <div class="card-body" style="min-height: 300px;">
                    @php
                        $months = [
                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                        ];
                        $maxTotal = $monthlyPerformance->max('total') ?: 1;
                    @endphp
                    @forelse($monthlyPerformance as $perf)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $months[(int)$perf->month] }}</span>
                                <span class="fw-bold text-success">${{ number_format($perf->total, 2, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($perf->total / $maxTotal) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fw-semibold mb-0 text-center mt-5">Aún no hay ingresos registrados este año</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-pie-chart me-2"></i>Top 5 Tratamientos</span>
                </div>
                <div class="card-body" style="min-height: 300px;">
                    <ul class="list-group list-group-flush">
                        @forelse($topTreatments as $top)
                            <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i class="bi bi-star-fill text-warning me-2"></i>
                                    {{ $top->treatment->name ?? 'N/A' }}
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $top->count }} contrataciones</span>
                            </li>
                        @empty
                            <p class="text-muted fw-semibold mb-0 text-center mt-5">Aún no hay tratamientos registrados</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
