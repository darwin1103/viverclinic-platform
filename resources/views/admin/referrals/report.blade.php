@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Reporte de Referidos</h4>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-send-check me-2"></i>Pacientes Referidores</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Paciente Referidor</th>
                        <th>Cantidad Referidos</th>
                        <th>Impacto Económico</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrers as $referrer)
                        <tr>
                            <td>{{ $referrer->name }}</td>
                            <td>{{ $referrer->referrals_count }}</td>
                            <td>${{ number_format($referrer->economic_impact, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center fw-semibold py-3 text-muted">Aún no hay referidos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
