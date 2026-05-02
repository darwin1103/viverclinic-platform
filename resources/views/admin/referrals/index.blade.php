@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-send-check me-2"></i>Gestión de Referidos</h4>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Total referidos</div>
                            <div class="kpi-value mt-1">{{ $totalReferrals }}</div>
                        </div>
                        <i class="bi bi-people fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Recompensados</div>
                            <div class="kpi-value mt-1">{{ $rewardedCount }}</div>
                        </div>
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Sesiones otorgadas</div>
                            <div class="kpi-value mt-1">{{ $totalBonusSessions }}</div>
                        </div>
                        <i class="bi bi-calendar-plus fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card kpi h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-secondary small">Comisiones pendientes</div>
                            <div class="kpi-value mt-1">${{ number_format($pendingCommissions, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-cash-coin fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.referrals.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small mb-1">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Nombre o email..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registrado</option>
                        <option value="rewarded" {{ request('status') === 'rewarded' ? 'selected' : '' }}>Recompensado</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.referrals.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de referidos --}}
    <div class="card">
        <div class="card-header fw-semibold">
            <i class="bi bi-table me-2"></i>Listado de referidos
            <span class="badge bg-secondary ms-2">{{ $referrals->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Referidor</th>
                        <th>Referido</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Sesiones</th>
                        <th>Empleada</th>
                        <th>Comisión</th>
                        <th>Estado comisión</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $referral->referrer->name ?? '-' }}</div>
                            <small class="text-secondary">{{ $referral->referrer->email ?? '' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $referral->referred->name ?? '-' }}</div>
                            <small class="text-secondary">{{ $referral->referred->email ?? '' }}</small>
                        </td>
                        <td>
                            <small>{{ $referral->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            @if($referral->status === 'rewarded')
                                <span class="badge bg-success">Recompensado</span>
                            @elseif($referral->status === 'registered')
                                <span class="badge bg-warning text-dark">Registrado</span>
                            @else
                                <span class="badge bg-secondary">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            @if($referral->bonus_sessions > 0)
                                <span class="fw-bold text-success">+{{ $referral->bonus_sessions }}</span>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $referral->staff->name ?? '-' }}
                        </td>
                        <td>
                            @if($referral->staff_commission)
                                ${{ number_format($referral->staff_commission, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($referral->staff_commission_status === 'paid')
                                <span class="badge bg-success">Pagada</span>
                            @elseif($referral->staff_commission_status === 'pending')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            @if($referral->staff_commission_status === 'pending')
                                <form method="POST" action="{{ route('admin.referrals.mark-commission-paid', $referral) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Marcar comisión como pagada?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como pagada">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-secondary">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            No hay referidos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($referrals->hasPages())
        <div class="card-footer">
            {{ $referrals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
