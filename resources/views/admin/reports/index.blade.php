@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Reportes y Analíticas</h4>
    </div>

    <div class="row g-3">

        {{-- 1. Monthly Performance --}}
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

        {{-- 2. Top Treatments --}}
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

        {{-- 3. Appointment Summary --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-calendar-check me-2"></i>Resumen de Citas (Mes actual)
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-3">
                            <div class="fs-3 fw-bold text-info">{{ $totalAppointments }}</div>
                            <small class="text-secondary">Total</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fs-3 fw-bold text-success">{{ $attendedAppointments }}</div>
                            <small class="text-secondary">Atendidas</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fs-3 fw-bold text-danger">{{ $cancelledAppointments }}</div>
                            <small class="text-secondary">Canceladas</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fs-3 fw-bold text-warning">{{ $noShowAppointments }}</div>
                            <small class="text-secondary">No asistió</small>
                        </div>
                    </div>
                    <hr class="border-secondary">
                    <div class="text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-clipboard-data fs-4 text-info"></i>
                            <div>
                                <span class="fs-4 fw-bold">{{ $attendanceRate }}%</span>
                                <small class="text-secondary d-block">Tasa de asistencia</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Staff Performance --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-people me-2"></i>Rendimiento del Staff (Mes actual)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Nombre</th>
                                    <th class="text-center">Citas completadas</th>
                                    <th class="text-center">Calificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffPerformance as $staff)
                                <tr>
                                    <td class="ps-3">{{ $staff->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $staff->completed_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($staff->avg_rating)
                                            <span class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= round($staff->avg_rating) ? '-fill' : '' }}"></i>
                                                @endfor
                                            </span>
                                            <small class="text-muted ms-1">({{ number_format($staff->avg_rating, 1) }})</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Sin datos para este mes</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Patient Retention --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-person-hearts me-2"></i>Retención de Pacientes
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4 text-center">
                            <i class="bi bi-person-plus fs-2 text-info d-block mb-2"></i>
                            <div class="fs-3 fw-bold">{{ $newPatients }}</div>
                            <small class="text-secondary">Nuevos (30d)</small>
                        </div>
                        <div class="col-4 text-center">
                            <i class="bi bi-arrow-repeat fs-2 text-success d-block mb-2"></i>
                            <div class="fs-3 fw-bold">{{ $recurringPatients }}</div>
                            <small class="text-secondary">Recurrentes</small>
                        </div>
                        <div class="col-4 text-center">
                            <i class="bi bi-send-check fs-2 text-warning d-block mb-2"></i>
                            <div class="fs-3 fw-bold">{{ $convertedReferrals }}</div>
                            <small class="text-secondary">Referidos exitosos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Revenue by Payment Method --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-credit-card me-2"></i>Ingresos por Método de Pago (Mes actual)
                </div>
                <div class="card-body">
                    @forelse($revenueByMethod as $method)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $method->method }}</span>
                                <div>
                                    <span class="fw-bold text-success">${{ number_format($method->total, 0, ',', '.') }}</span>
                                    <small class="text-muted ms-1">({{ $method->count }} pagos)</small>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($method->total / $maxMethodTotal) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fw-semibold mb-0 text-center mt-3">Sin pagos registrados este mes</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
