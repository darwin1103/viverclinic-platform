@extends('layouts.admin')
@section('content')
<div class="container-fluid">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Detalle de Liquidación</h4>
            <p class="text-muted mb-0">{{ $settlement->user->name ?? '-' }} — {{ $months[$month] }} {{ $year }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($settlement->status === 'pending')
                <form method="POST" action="{{ route('admin.payroll.recalculate', $settlement) }}"
                      onsubmit="return confirm('¿Recalcular la liquidación con los datos actuales?');">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-clockwise me-1"></i>Recalcular
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.payroll.mark-paid', $settlement) }}"
                      onsubmit="return confirm('¿Marcar como pagada la liquidación de {{ $settlement->user->name ?? '' }}?');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Marcar como Pagada
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.payroll.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-primary">
                <i class="bi bi-arrow-left-circle me-1"></i>Volver
            </a>
        </div>
    </div>

    {{-- Summary Card --}}
    @php
        $totalBonuses = $settlement->manualBonuses->sum('amount');
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="text-secondary small">Sueldo Base</div>
                    <div class="kpi-value mt-1">${{ number_format($settlement->base_salary, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="text-secondary small">Total Comisiones</div>
                    <div class="kpi-value mt-1">${{ number_format($settlement->referral_commissions + $settlement->upgrade_commissions + $settlement->repurchase_commissions, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="text-secondary small">Bonos Manuales</div>
                    <div class="kpi-value mt-1">${{ number_format($totalBonuses, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="text-secondary small">Total a Pagar</div>
                    <div class="kpi-value mt-1 text-success">${{ number_format($settlement->total, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($settlement->role_type === 'EMPLOYEE')

    {{-- Referral Commissions --}}
    <div class="card mb-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-send-check me-2 text-info"></i>Comisiones por Referidos</span>
            <span class="badge bg-info">{{ $referralEntries->count() }} {{ $referralEntries->count() === 1 ? 'entrada' : 'entradas' }} — ${{ number_format($settlement->referral_commissions, 0, ',', '.') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Fecha</th>
                        <th>Cliente Referido</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referralEntries as $entry)
                        <tr>
                            <td class="ps-3">{{ $entry->rewarded_at?->format('d/m/Y') }}</td>
                            <td>{{ $entry->referred->name ?? 'N/A' }}</td>
                            <td class="fw-semibold">${{ number_format($entry->staff_commission, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-3 text-muted">Sin comisiones por referidos este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upgrade Commissions --}}
    <div class="card mb-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-arrow-up-right-circle me-2 text-warning"></i>Comisiones por Agrandamientos</span>
            <span class="badge bg-warning text-dark">{{ $upgradeEntries->count() }} {{ $upgradeEntries->count() === 1 ? 'entrada' : 'entradas' }} — ${{ number_format($settlement->upgrade_commissions, 0, ',', '.') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Fecha</th>
                        <th>Cliente</th>
                        <th>Paquete</th>
                        <th>Diferencia</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upgradeEntries as $entry)
                        <tr>
                            <td class="ps-3">{{ $entry->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($entry->contractedTreatment)
                                    <a href="{{ route('admin.contracted-treatment.show', $entry->contracted_treatment_id) }}" class="text-decoration-none">
                                        {{ $entry->contractedTreatment->user->name ?? 'N/A' }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($entry->contractedTreatment?->treatment)
                                    <a href="{{ route('admin.contracted-treatment.show', $entry->contracted_treatment_id) }}" class="text-decoration-none">
                                        {{ $entry->contractedTreatment->treatment->name ?? '-' }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>${{ number_format($entry->price_difference, 0, ',', '.') }}</td>
                            <td class="fw-semibold">${{ number_format($entry->commission_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Sin comisiones por agrandamientos este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Repurchase Commissions --}}
    <div class="card mb-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-arrow-repeat me-2 text-primary"></i>Comisiones por Recompras</span>
            <span class="badge bg-primary">{{ $repurchaseEntries->count() }} {{ $repurchaseEntries->count() === 1 ? 'entrada' : 'entradas' }} — ${{ number_format($settlement->repurchase_commissions, 0, ',', '.') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Fecha</th>
                        <th>Cliente</th>
                        <th>Paquete Contratado</th>
                        <th>Total Tratamiento</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repurchaseEntries as $entry)
                        <tr>
                            <td class="ps-3">{{ $entry->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($entry->contractedTreatment)
                                    <a href="{{ route('admin.contracted-treatment.show', $entry->contracted_treatment_id) }}" class="text-decoration-none">
                                        {{ $entry->contractedTreatment->user->name ?? 'N/A' }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($entry->contractedTreatment?->treatment)
                                    <a href="{{ route('admin.contracted-treatment.show', $entry->contracted_treatment_id) }}" class="text-decoration-none">
                                        {{ $entry->contractedTreatment->treatment->name ?? '-' }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>${{ number_format($entry->treatment_total, 0, ',', '.') }}</td>
                            <td class="fw-semibold">${{ number_format($entry->commission_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Sin comisiones por recompras este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

    {{-- Manual Bonuses Section --}}
    <div class="card mb-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-gift me-2 text-success"></i>Bonos Manuales</span>
            @if($totalBonuses > 0)
                <span class="badge bg-success">${{ number_format($totalBonuses, 0, ',', '.') }}</span>
            @endif
        </div>
        <div class="card-body p-0">
            {{-- Existing bonus entries --}}
            @if($settlement->manualBonuses->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Fecha</th>
                                <th>Monto</th>
                                <th>Nota</th>
                                @if($settlement->status === 'pending')
                                    <th style="width: 180px;">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settlement->manualBonuses as $bonus)
                                <tr>
                                    @if($settlement->status === 'pending')
                                        <td colspan="4" class="p-0">
                                            <form method="POST" action="{{ route('admin.payroll.manual-bonus.update', [$settlement, $bonus]) }}" class="d-flex align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <div class="ps-3 py-2" style="width: 20%;">
                                                    {{ $bonus->created_at->format('d/m/Y') }}
                                                </div>
                                                <div class="py-1" style="width: 25%;">
                                                    <input type="text" inputmode="numeric" name="amount" class="form-control form-control-sm currency-input"
                                                           value="{{ $bonus->amount }}" required>
                                                </div>
                                                <div class="px-2 py-1" style="flex: 1;">
                                                    <input type="text" name="note" class="form-control form-control-sm"
                                                           value="{{ $bonus->note }}" placeholder="Nota...">
                                                </div>
                                                <div class="pe-3 py-1 d-flex gap-1" style="width: 180px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Guardar">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                            onclick="if(confirm('¿Eliminar este bono?')) document.getElementById('deleteBonus{{ $bonus->id }}').submit();">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </form>
                                            <form id="deleteBonus{{ $bonus->id }}" method="POST"
                                                  action="{{ route('admin.payroll.manual-bonus.delete', [$settlement, $bonus]) }}" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    @else
                                        <td class="ps-3">{{ $bonus->created_at->format('d/m/Y') }}</td>
                                        <td class="fw-semibold">${{ number_format($bonus->amount, 0, ',', '.') }}</td>
                                        <td>{{ $bonus->note ?? '-' }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                @if($settlement->status !== 'pending')
                    <div class="p-3 text-muted">No se asignaron bonos manuales para esta liquidación.</div>
                @endif
            @endif

            {{-- Add new bonus form --}}
            @if($settlement->status === 'pending')
                <div class="border-top p-3">
                    <form method="POST" action="{{ route('admin.payroll.manual-bonus', $settlement) }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold mb-1">Monto (COP)</label>
                            <input type="text" inputmode="numeric" name="amount" class="form-control form-control-sm currency-input"
                                   placeholder="0" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold mb-1">Nota (opcional)</label>
                            <input type="text" name="note" class="form-control form-control-sm"
                                   placeholder="Motivo del bono...">
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i>Agregar Bono
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Sales Commission (for ADMIN/SALES roles) --}}
    @if($settlement->role_type !== 'EMPLOYEE' && $settlement->sales_commissions > 0)
    <div class="card mb-3">
        <div class="card-header fw-semibold">
            <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Comisión por Ventas
        </div>
        <div class="card-body">
            <span class="fw-bold fs-5">${{ number_format($settlement->sales_commissions, 0, ',', '.') }}</span>
            <span class="text-muted ms-2">Calculada sobre ventas del mes</span>
        </div>
    </div>
    @endif

</div>
@endsection
