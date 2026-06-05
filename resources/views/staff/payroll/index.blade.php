@extends('layouts.employee')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Mi Liquidación</h1>
            <p class="text-muted">Resumen de tu sueldo base y comisiones del mes actual.</p>
        </div>
    </div>

    {{-- Unified Commission Target --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-muted mb-3"><i class="bi bi-trophy me-2"></i>Meta de Comisiones del Mes</h5>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <span class="fs-4 fw-bold text-info">${{ number_format($totalCommissions, 0, ',', '.') }}</span>
                        <span class="text-muted">de ${{ number_format($commissionTarget, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $commissionProgress >= 100 ? 'success' : 'info' }} progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width: {{ $commissionProgress }}%;"
                             aria-valuenow="{{ $commissionProgress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format($commissionProgress, 1) }}%
                        </div>
                    </div>
                    @if($commissionProgress >= 100 && $commissionTarget > 0)
                        <div class="text-success mt-2 fw-bold"><i class="bi bi-stars"></i> ¡Felicidades! Has alcanzado tu meta de comisiones este mes.</div>
                    @endif

                    {{-- Breakdown by source --}}
                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-4 text-center">
                            <i class="bi bi-send-check text-info"></i>
                            <div class="small text-muted">Referidos</div>
                            <div class="fw-bold">${{ number_format($currentReferralCommissions, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-4 text-center">
                            <i class="bi bi-arrow-up-right-circle text-warning"></i>
                            <div class="small text-muted">Agrandamientos</div>
                            <div class="fw-bold">${{ number_format($currentUpgradeCommissions, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-4 text-center">
                            <i class="bi bi-arrow-repeat text-primary"></i>
                            <div class="small text-muted">Recompras</div>
                            <div class="fw-bold">${{ number_format($currentRepurchaseCommissions, 0, ',', '.') }}</div>
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
                                    <th>Referidos</th>
                                    <th>Agrandamientos</th>
                                    <th>Recompras</th>
                                    <th>Bono</th>
                                    <th>Total Pagado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settlements as $settlement)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($settlement->created_at)->format('d/m/Y') }}</td>
                                        <td>${{ number_format($settlement->base_salary, 0, ',', '.') }}</td>
                                        <td>${{ number_format($settlement->referral_commissions, 0, ',', '.') }}</td>
                                        <td>${{ number_format($settlement->upgrade_commissions, 0, ',', '.') }}</td>
                                        <td>${{ number_format($settlement->repurchase_commissions, 0, ',', '.') }}</td>
                                        <td>
                                            @if(($settlement->manual_bonus ?? 0) > 0)
                                                ${{ number_format($settlement->manual_bonus, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($settlement->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No tienes liquidaciones registradas en el historial.</td>
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
