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
                    <div class="text-secondary small">Comisión por Ventas</div>
                    <div class="kpi-value mt-1">${{ number_format($settlement->commission_amount, 0, ',', '.') }}</div>
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

    {{-- Ingreso manual de comisión --}}
    @if($settlement->status === 'pending')
    <div class="card mb-3 border-info">
        <div class="card-header bg-info bg-opacity-10 text-info fw-bold">
            <i class="bi bi-cash-stack me-2"></i>Asignar Comisión por Ventas
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.payroll.update-commission', $settlement) }}" class="row g-2 align-items-end">
                @csrf
                @method('PUT')
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold">Monto de la Comisión (COP)</label>
                    <input type="text" inputmode="numeric" name="commission_amount" class="form-control currency-input"
                           value="{{ $settlement->commission_amount }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-info text-white w-100">
                        <i class="bi bi-save me-1"></i>Guardar Comisión
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Sales Registradas --}}
    <div class="card mb-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cart-check me-2 text-success"></i>Ventas Registradas del Mes</span>
            <span class="badge bg-success">{{ $salesCount }} {{ $salesCount === 1 ? 'venta' : 'ventas' }} — Total: ${{ number_format($salesTotal, 0, ',', '.') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Fecha</th>
                        <th>Cliente</th>
                        <th>Tipo de Venta</th>
                        <th>Monto del Primer Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="ps-3">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->patient->name ?? 'N/A' }}</td>
                            <td>
                                @if($sale->type === 'referral')
                                    <span class="badge bg-info">Referido</span>
                                @elseif($sale->type === 'upgrade')
                                    <span class="badge bg-warning text-dark">Agrandamiento</span>
                                @elseif($sale->type === 'repurchase')
                                    <span class="badge bg-primary">Recompra</span>
                                @else
                                    <span class="badge bg-secondary">{{ $sale->type }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold">${{ number_format($sale->first_payment_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($sale->contracted_treatment_id)
                                    <a href="{{ route('admin.contracted-treatment.show', $sale->contracted_treatment_id) }}" class="btn btn-sm btn-outline-info" title="Ver paquete del que proviene esta venta">
                                        <i class="bi bi-box-arrow-up-right"></i> Ver Paquete
                                    </a>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No se registraron ventas para este empleado en este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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

</div>
@endsection
