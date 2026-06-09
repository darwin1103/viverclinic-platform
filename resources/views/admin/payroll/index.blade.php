@extends('layouts.admin')
@section('content')
<div class="container-fluid">

    {{-- Title --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i>Liquidación Mensual</h4>
    </div>

    {{-- Month/Year Selector --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.payroll.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Mes</label>
                    <select name="month" class="form-select form-select-sm">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small mb-1">Año</label>
                    <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" min="2020" max="2099">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>Consultar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Total a liquidar</div>
                            <div class="kpi-value mt-1">${{ number_format($totalToSettle, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-cash-stack fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Pendientes</div>
                            <div class="kpi-value mt-1">{{ $pendingCount }}</div>
                        </div>
                        <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Pagadas</div>
                            <div class="kpi-value mt-1">{{ $paidCount }}</div>
                        </div>
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Generate Button --}}
    @if($settlements->isEmpty())
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-3">No se ha generado liquidación para {{ $months[$month] }} {{ $year }}.</p>
                <form method="POST" action="{{ route('admin.payroll.generate') }}" onsubmit="return confirm('¿Generar liquidación para {{ $months[$month] }} {{ $year }}?');">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calculator me-1"></i>Generar Liquidación del Mes
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Settlements Table --}}
    @if($settlements->isNotEmpty())
    <div class="card">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-2"></i>Liquidación {{ $months[$month] }} {{ $year }}</span>
            <span class="badge bg-secondary">{{ $settlements->count() }} registros</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Nombre</th>
                        <th>Rol</th>
                        <th>Sucursal</th>
                        <th>Sueldo Base</th>
                        <th>Referidos</th>
                        <th>Agrandamientos</th>
                        <th>Recompras</th>
                        <th>Ventas</th>
                        <th>Bono Manual</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settlements as $settlement)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $settlement->user->name ?? '-' }}</td>
                        <td>
                            @if($settlement->role_type === 'EMPLOYEE')
                                <span class="badge bg-info">Empleada</span>
                            @elseif($settlement->role_type === 'SALES')
                                <span class="badge bg-secondary">Ventas</span>
                            @else
                                <span class="badge bg-primary">Admin</span>
                            @endif
                        </td>
                        <td>{{ $settlement->branch->name ?? '-' }}</td>
                        <td>${{ number_format($settlement->base_salary, 0, ',', '.') }}</td>
                        <td>
                            @if($settlement->role_type === 'EMPLOYEE')
                                ${{ number_format($settlement->referral_commissions, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($settlement->role_type === 'EMPLOYEE')
                                ${{ number_format($settlement->upgrade_commissions, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($settlement->role_type === 'EMPLOYEE')
                                ${{ number_format($settlement->repurchase_commissions, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($settlement->role_type !== 'EMPLOYEE')
                                ${{ number_format($settlement->sales_commissions, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if(($settlement->manual_bonus ?? 0) > 0)
                                <span title="{{ $settlement->manual_bonus_note ?? '' }}">${{ number_format($settlement->manual_bonus, 0, ',', '.') }}</span>
                                @if($settlement->manual_bonus_note)
                                    <i class="bi bi-info-circle text-info small" title="{{ $settlement->manual_bonus_note }}"></i>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-bold">${{ number_format($settlement->total, 0, ',', '.') }}</td>
                        <td>
                            @if($settlement->status === 'paid')
                                <span class="badge bg-success">Pagada</span>
                                <small class="text-muted d-block">{{ $settlement->paid_at?->format('d/m/Y') }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            @if($settlement->status === 'pending')
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.payroll.show', $settlement) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.payroll.mark-paid', $settlement) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Marcar como pagada la liquidación de {{ $settlement->user->name ?? '' }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como pagada">
                                            <i class="bi bi-check-lg me-1"></i>Pagar
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('admin.payroll.show', $settlement) }}" class="btn btn-sm btn-outline-secondary" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @endif
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
