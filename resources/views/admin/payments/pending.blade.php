@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Pagos Pendientes</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Pagos por Verificar</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                            <td>{{ $payment->user->name ?? 'N/A' }}</td>
                            <td>{{ $payment->contractedTreatment->treatment->name ?? 'N/A' }}</td>
                            <td>${{ number_format($payment->total, 2, ',', '.') }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $payment->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center fw-semibold py-3 text-muted">Aún no hay pagos pendientes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
