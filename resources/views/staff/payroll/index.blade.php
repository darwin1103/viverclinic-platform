@extends('layouts.employee')
@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold">Mi Liquidación</h1>
            <p class="text-muted">Resumen de tu sueldo base y comisiones del mes actual.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Meta de Referidos --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-muted mb-3"><i class="bi bi-send-check me-2"></i>Meta de Referidos</h5>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <span class="fs-4 fw-bold text-info">${{ number_format($currentReferralCommissions, 0, ',', '.') }}</span>
                        <span class="text-muted">de ${{ number_format($referralTarget, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $referralProgress >= 100 ? 'success' : 'info' }} progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ $referralProgress }}%;" 
                             aria-valuenow="{{ $referralProgress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($referralProgress, 1) }}%
                        </div>
                    </div>
                    @if($referralProgress >= 100 && $referralTarget > 0)
                        <div class="text-success mt-2 fw-bold"><i class="bi bi-stars"></i> ¡Felicidades! Has alcanzado tu meta de referidos este mes.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Meta de Agrandamientos --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-muted mb-3"><i class="bi bi-arrow-up-right-circle me-2"></i>Meta de Agrandamientos</h5>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <span class="fs-4 fw-bold text-warning">${{ number_format($currentUpgradeCommissions, 0, ',', '.') }}</span>
                        <span class="text-muted">de ${{ number_format($upgradeTarget, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $upgradeProgress >= 100 ? 'success' : 'warning' }} progress-bar-striped progress-bar-animated {{ $upgradeProgress >= 100 ? '' : 'text-dark' }}" 
                             role="progressbar" 
                             style="width: {{ $upgradeProgress }}%;" 
                             aria-valuenow="{{ $upgradeProgress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($upgradeProgress, 1) }}%
                        </div>
                    </div>
                    @if($upgradeProgress >= 100 && $upgradeTarget > 0)
                        <div class="text-success mt-2 fw-bold"><i class="bi bi-stars"></i> ¡Felicidades! Has alcanzado tu meta de agrandamientos este mes.</div>
                    @endif
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
                                    <th>Total Pagado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settlements as $settlement)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($settlement->created_at)->format('d/m/Y') }}</td>
                                        <td>${{ number_format($settlement->base_salary, 0, ',', '.') }}</td>
                                        <td>${{ number_format($settlement->referral_commissions, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $upgradeComm = $settlement->upgrade_commissions ?: $settlement->sales_commissions;
                                            @endphp
                                            ${{ number_format($upgradeComm, 0, ',', '.') }}
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
