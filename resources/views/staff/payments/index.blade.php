@extends('layouts.employee')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Pagos</h4>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cash-coin me-2"></i>Historial de Pagos</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Monto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                            <td>{{ $payment->user->name ?? 'N/A' }}</td>
                            <td>${{ number_format($payment->total, 2, ',', '.') }}</td>
                            <td>
                                @if(in_array(strtolower($payment->status), ['pagado', 'completado']))
                                    <span class="badge bg-success">{{ $payment->status }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $payment->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center fw-semibold py-3 text-muted">Aún no hay pagos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
