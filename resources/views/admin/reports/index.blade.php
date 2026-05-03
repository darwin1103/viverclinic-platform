@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Reportes y Analíticas</h4>
    </div>

    {{-- Filters --}}
    <div class="card mb-4 border-0" style="background-color: transparent;">
        <div class="card-body p-0">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label small text-secondary mb-1">Desde</label>
                    <input type="date" name="from" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $from }}">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label small text-secondary mb-1">Hasta</label>
                    <input type="date" name="to" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $to }}">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabs de Navegación --}}
    <ul class="nav nav-tabs mb-4 border-secondary border-opacity-50" id="reportsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="shots-tab" data-bs-toggle="tab" data-bs-target="#shots" type="button" role="tab" aria-controls="shots" aria-selected="false">
                <i class="bi bi-bullseye me-2"></i>Control de Disparos
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reportsTabsContent">
        {{-- TAB 1: DASHBOARD GENERAL --}}
        <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

    {{-- 6 KPIs principales solicitados por el usuario --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow border-0" style="background: linear-gradient(135deg, #1e2a38 0%, #161f2b 100%);">
                <div class="card-body text-center p-3">
                    <i class="bi bi-person-plus fs-3 text-info mb-2 d-block"></i>
                    <h3 class="fw-bold text-white mb-0">{{ $newPatients }}</h3>
                    <small class="text-secondary" style="font-size: 0.8rem;">Pacientes Nuevos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow border-0" style="background: linear-gradient(135deg, #1e2a38 0%, #161f2b 100%);">
                <div class="card-body text-center p-3">
                    <i class="bi bi-award fs-3 text-success mb-2 d-block"></i>
                    <h3 class="fw-bold text-white mb-0">{{ $finishedPatients }}</h3>
                    <small class="text-secondary" style="font-size: 0.8rem;">Pacientes Finalizados</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow border-0" style="background: linear-gradient(135deg, #1e2a38 0%, #161f2b 100%);">
                <div class="card-body text-center p-3">
                    <i class="bi bi-cash-coin fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="fw-bold text-white mb-0" style="font-size: 1.2rem;">${{ number_format($treatmentIncome, 0, ',', '.') }}</h3>
                    <small class="text-secondary" style="font-size: 0.8rem;">Ingresos Tratamientos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow border-0" style="background: linear-gradient(135deg, #1e2a38 0%, #161f2b 100%);">
                <div class="card-body text-center p-3">
                    <i class="bi bi-bag-check fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="fw-bold text-white mb-0" style="font-size: 1.2rem;">${{ number_format($productIncome, 0, ',', '.') }}</h3>
                    <small class="text-secondary" style="font-size: 0.8rem;">Ingresos Productos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow border-0" style="background: linear-gradient(135deg, #1e2a38 0%, #161f2b 100%);">
                <div class="card-body text-center p-3">
                    <i class="bi bi-graph-down-arrow fs-3 text-danger mb-2 d-block"></i>
                    <h3 class="fw-bold text-white mb-0" style="font-size: 1.2rem;">${{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                    <small class="text-secondary" style="font-size: 0.8rem;">Gastos Totales</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow border-0" style="background: linear-gradient(135deg, #1e2a38 0%, #161f2b 100%);">
                <div class="card-body text-center p-3">
                    <i class="bi bi-people fs-3" style="color: #c971ff; margin-bottom: 0.5rem; display: block;"></i>
                    <h3 class="fw-bold text-white mb-0">{{ $attendedPatients }}</h3>
                    <small class="text-secondary" style="font-size: 0.8rem;">Pacientes Atendidos</small>
                </div>
            </div>
        </div>
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

        {{-- TAB 2: CONTROL DE DISPAROS --}}
        <div class="tab-pane fade" id="shots" role="tabpanel" aria-labelledby="shots-tab">
            <div class="card border-0 shadow">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-bullseye me-2"></i>Registro de Disparos en Tratamientos
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Paciente</th>
                                    <th>Tratamiento y Zonas</th>
                                    <th>Atendido por</th>
                                    <th class="text-center">Límite</th>
                                    <th class="text-center">Utilizados</th>
                                    <th class="text-center">Exceso</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shotsRecords as $record)
                                    @php
                                        $limit = $record->max_shots_limit;
                                        $used = $record->uses_of_hair_removal_shots;
                                        $excess = max(0, $used - $limit);
                                        
                                        // Highlight logic
                                        $rowClass = '';
                                        $statusBadge = '<span class="badge bg-success">Normal</span>';
                                        
                                        if ($excess > 100) {
                                            $rowClass = 'table-highlight-primary';
                                            $statusBadge = '<span class="badge bg-danger">Exceso Crítico</span>';
                                        } elseif ($excess > 0) {
                                            $rowClass = 'table-highlight-primary';
                                            $statusBadge = '<span class="badge bg-primary text-white">Exceso Leve</span>';
                                        }
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{ \Carbon\Carbon::parse($record->schedule)->format('d M Y, h:i A') }}</td>
                                        <td>{{ $record->contractedTreatment?->user?->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $record->contractedTreatment?->treatment?->name ?? 'N/A' }}</div>
                                            <small class="text-secondary">
                                                @php
                                                    $allZones = [];
                                                    $rawZones = $record->contractedTreatment?->selected_zones ?? [];
                                                    if (is_array($rawZones)) {
                                                        foreach ($rawZones as $cat => $zones) {
                                                            if (is_array($zones)) {
                                                                $allZones = array_merge($allZones, $zones);
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if(!empty($allZones))
                                                    {{ implode(', ', $allZones) }}
                                                @else
                                                    Sin zonas específicas
                                                @endif
                                            </small>
                                        </td>
                                        <td>{{ $record->staff?->name ?? 'N/A' }}</td>
                                        <td class="text-center fw-bold">{{ $limit }}</td>
                                        <td class="text-center fw-bold fs-5">{{ $used }}</td>
                                        <td class="text-center">
                                            @if($excess > 0)
                                                <span class="text-danger fw-bold">+{{ $excess }}</span>
                                            @else
                                                <span class="text-success">-</span>
                                            @endif
                                        </td>
                                        <td>{!! $statusBadge !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-secondary">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay registros de disparos reportados en este periodo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- End Tab Content --}}
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración de gráficas (Dashboard General)
    // ... (el código JS existente sigue funcionando igual) ...
</script>
@endpush
