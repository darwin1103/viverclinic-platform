@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Historial de Pagos</h4>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Registrar Pago
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Buscar paciente</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre del paciente..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Método</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" {{ request('payment_method') === $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cash-coin me-2"></i>Todos los Pagos</span>
            <span class="badge bg-secondary">{{ $payments->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Fecha</th>
                        <th>Tipo</th>
                        <th>Paciente</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="ps-3">{{ $payment->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($payment->payment_type === 'treatment')
                                    <span class="badge bg-info">Tratamiento</span>
                                @else
                                    <span class="badge bg-secondary">Producto</span>
                                @endif
                            </td>
                            <td>{{ $payment->user->name ?? 'N/A' }}</td>
                            <td>{{ $payment->concept ?? 'N/A' }}</td>
                            <td class="fw-bold">${{ number_format($payment->total, 0, ',', '.') }}</td>
                            <td>{{ $payment->payment_method ?? '-' }}</td>
                            <td>
                                @php
                                    $statusLower = strtolower($payment->status);
                                    $badgeClass = match(true) {
                                        in_array($statusLower, ['pagado', 'completado', 'pago completado', 'paid']) => 'bg-success',
                                        in_array($statusLower, ['cancelado', 'declined']) => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $payment->status }}</span>
                            </td>
                            <td>
                                @if(in_array($payment->status, ['Pago por verificar', 'Pendiente', 'Pending']))
                                    <div class="d-flex gap-1">
                                        @php
                                            $approveRoute = $payment->payment_type === 'product' ? route('admin.payments.product.approve', $payment->id) : route('admin.payments.approve', $payment->id);
                                            $rejectRoute = $payment->payment_type === 'product' ? route('admin.payments.product.reject', $payment->id) : route('admin.payments.reject', $payment->id);
                                        @endphp
                                        <form method="POST" action="{{ $approveRoute }}"
                                              onsubmit="return confirm('¿Aprobar este pago?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Aprobar">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ $rejectRoute }}"
                                              onsubmit="return confirm('¿Rechazar este pago?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Rechazar">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center fw-semibold py-3 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No hay pagos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
