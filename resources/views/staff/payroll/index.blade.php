@extends('layouts.employee')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Mi Liquidación</h1>
            <p class="text-muted">Resumen de tu sueldo base y ventas del mes actual.</p>
        </div>
    </div>

    {{-- Unified Sales Target --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-muted mb-3"><i class="bi bi-trophy me-2"></i>Meta de Ventas del Mes</h5>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <span class="fs-4 fw-bold text-info">${{ number_format($currentSalesTotal, 0, ',', '.') }}</span>
                        <span class="text-muted">de ${{ number_format($salesTarget, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $salesProgress >= 100 ? 'success' : 'info' }} progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width: {{ $salesProgress }}%;"
                             aria-valuenow="{{ $salesProgress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format($salesProgress, 1) }}%
                        </div>
                    </div>
                    @if($salesProgress >= 100 && $salesTarget > 0)
                        <div class="text-success mt-2 fw-bold"><i class="bi bi-stars"></i> ¡Felicidades! Has alcanzado tu meta de ventas este mes.</div>
                    @endif

                    {{-- Summary --}}
                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12 text-center">
                            <i class="bi bi-cart-check text-success fs-3"></i>
                            <div class="small text-muted mt-1">Ventas Registradas Este Mes</div>
                            <div class="fw-bold fs-4">{{ $currentSalesCount }}</div>
                        </div>
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
