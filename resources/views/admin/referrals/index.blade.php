@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0 fw-semibold text-white">Gestión de Referidos</h4>
        <a href="{{ route('admin.referrals.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Registrar Referido
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-send-check me-2"></i>Lista de Referidos</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Paciente que Refiere</th>
                        <th>Nombre del Referido</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $referral->referrer->name ?? 'N/A' }}</td>
                            <td>{{ $referral->referred_name }}</td>
                            <td>
                                @if($referral->referred_email)<div class="small">{{ $referral->referred_email }}</div>@endif
                                @if($referral->referred_phone)<div class="small text-muted">{{ $referral->referred_phone }}</div>@endif
                            </td>
                            <td>
                                @if(in_array(strtolower($referral->status), ['completado', 'completed', 'aprobado']))
                                    <span class="badge bg-success">{{ $referral->status }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $referral->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center fw-semibold py-3 text-muted">Aún no hay referidos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
