@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <h1 class="">Mi Billetera Virtual</h1>

    {{-- Tarjeta Superior Estilo Filtro --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-muted mb-1"><i class="bi bi-wallet2 me-2"></i>Saldo Disponible</h5>
                <h2 class="display-5 fw-bold mb-0">${{ number_format($wallet->balance ?? 0, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">COP</span></h2>
            </div>
        </div>
    </div>

    {{-- Tabla de Movimientos --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-white">Fecha</th>
                        <th class="text-white">Descripción</th>
                        <th class="text-center text-white">Tipo</th>
                        <th class="text-end text-white">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y') }}
                                <small class="text-muted d-block">{{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }}</small>
                            </td>
                            <td>{{ $trx->description }}</td>
                            <td class="text-center">
                                @if($trx->type == 'ingreso')
                                    <span class="badge bg-success">Ingreso</span>
                                @else
                                    <span class="badge bg-danger">Egreso</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $trx->type == 'ingreso' ? 'text-success' : 'text-danger' }}">
                                {{ $trx->type == 'ingreso' ? '+' : '-' }}${{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No tienes movimientos recientes en tu billetera.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
