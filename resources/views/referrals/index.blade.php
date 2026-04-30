@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <h1 class="">Mis Referidos</h1>

    {{-- Tarjeta Superior Estilo Filtro --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-muted mb-3"><i class="bi bi-share me-2"></i>Tu Código de Referido</h5>
            <div class="d-flex align-items-center flex-wrap gap-3">
                @if($referralCode)
                    <div class="bg-dark border border-secondary text-white fw-bold fs-4 rounded py-2 px-4 shadow-sm user-select-all" style="letter-spacing: 2px;" id="referralCodeText">
                        {{ $referralCode }}
                    </div>
                    <button class="btn btn-outline-light rounded-pill fw-medium" onclick="copyReferralCode()">
                        <i class="bi bi-clipboard me-2"></i>Copiar
                    </button>
                @else
                    <div class="bg-dark border border-secondary text-secondary opacity-50 fw-bold fs-4 rounded py-2 px-4 shadow-sm d-inline-block">
                        NO DISPONIBLE
                    </div>
                    <p class="text-muted small mb-0 ms-2">Comunícate con administración para generar tu código.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabla de Pacientes Invitados --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-white">Fecha de Registro</th>
                        <th class="text-white">Nombre del Paciente</th>
                        <th class="text-center text-white">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $ref)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($ref->created_at)->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $ref->referred_name }}</div>
                                <div class="text-muted small">{{ $ref->referred_email ?? 'Sin correo' }}</div>
                            </td>
                            <td class="text-center">
                                @if(strtolower($ref->status) == 'completed' || strtolower($ref->status) == 'completado')
                                    <span class="badge bg-success">Completado</span>
                                @elseif(strtolower($ref->status) == 'pending' || strtolower($ref->status) == 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @else
                                    <span class="badge bg-secondary">{{ $ref->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Aún no has invitado a ningún paciente. ¡Comparte tu código y gana beneficios!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyReferralCode() {
        var copyText = document.getElementById("referralCodeText").innerText.trim();
        navigator.clipboard.writeText(copyText).then(function() {
            alert("¡Código " + copyText + " copiado al portapapeles!");
        }, function(err) {
            console.error('Error al copiar: ', err);
        });
    }
</script>
@endpush
@endsection
