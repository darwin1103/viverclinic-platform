@extends('layouts.employee')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Mi Liquidación</h1>
            <p class="text-muted">Resumen de tu sueldo base y ventas del mes actual.</p>
        </div>
    </div>

    {{-- Metas Globales y Contribución Individual --}}
    <div class="row g-4 mb-4">
        {{-- Progreso del Equipo (Metas Globales) --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-muted mb-4"><i class="bi bi-trophy me-2"></i>Progreso del Equipo (Metas Mensuales)</h5>
                    
                    {{-- Meta de Monto --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="fw-semibold">Monto Total de Ventas</span>
                            <span>
                                <span class="fs-5 fw-bold text-info">${{ number_format($globalSalesTotal, 0, ',', '.') }}</span>
                                <span class="text-muted small">/ ${{ number_format($salesTargetAmount, 0, ',', '.') }}</span>
                            </span>
                        </div>
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-{{ $globalAmountProgress >= 100 ? 'success' : 'info' }} progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: {{ $globalAmountProgress }}%;">
                                {{ number_format($globalAmountProgress, 1) }}%
                            </div>
                        </div>
                    </div>

                    {{-- Meta de Cantidad --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="fw-semibold">Cantidad Total de Ventas</span>
                            <span>
                                <span class="fs-5 fw-bold text-primary">{{ $globalSalesCount }}</span>
                                <span class="text-muted small">/ {{ $salesTargetCount }} ventas</span>
                            </span>
                        </div>
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-{{ $globalCountProgress >= 100 ? 'success' : 'primary' }} progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: {{ $globalCountProgress }}%;">
                                {{ number_format($globalCountProgress, 1) }}%
                            </div>
                        </div>
                    </div>

                    @if($globalAmountProgress >= 100 && $globalCountProgress >= 100 && $salesTargetAmount > 0 && $salesTargetCount > 0)
                        <div class="text-success mt-4 fw-bold"><i class="bi bi-stars"></i> ¡Felicidades! El equipo ha alcanzado todas las metas del mes.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contribución Individual --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="fw-bold text-muted mb-4"><i class="bi bi-person-star me-2"></i>Tu Contribución</h5>
                    
                    <div class="mb-4 w-100">
                        <div class="text-muted small mb-1 text-uppercase fw-semibold">Monto Vendido</div>
                        <div class="fs-3 fw-bold text-info">${{ number_format($currentSalesTotal, 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="w-100">
                        <div class="text-muted small mb-1 text-uppercase fw-semibold">Ventas Realizadas</div>
                        <div class="fs-3 fw-bold text-primary">{{ $currentSalesCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="text-muted mb-1"><i class="bi bi-cash-stack me-2"></i>Sueldo Base</h5>
                    <h2 class="fw-bold m-0">${{ number_format($profile->salary, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-2"></i>Historial de Liquidaciones</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead class="border-bottom">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Sueldo Base</th>
                                    <th>Comisión por Ventas</th>
                                    <th>Bonos Manuales</th>
                                    <th>Total Pagado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settlements as $settlement)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($settlement->created_at)->format('d/m/Y') }}</td>
                                        <td>${{ number_format($settlement->base_salary, 0, ',', '.') }}</td>
                                        <td>${{ number_format($settlement->commission_amount, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $manualBonusesTotal = $settlement->manualBonuses->sum('amount');
                                            @endphp
                                            @if($manualBonusesTotal > 0)
                                                ${{ number_format($manualBonusesTotal, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($settlement->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No tienes liquidaciones registradas en el historial.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
