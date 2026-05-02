@extends('layouts.app')
@section('content')
<div class="container-fluid p-0 py-4">
    <h1 class="">Mis Referidos</h1>


    @if($referralEnabled)

    {{-- Tarjeta principal con el enlace de referido --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0" style="background: linear-gradient(135deg, #1a3a4a 0%, #0d2535 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-7">
                            <h4 class="fw-bold mb-2" style="color: #b6d3db;">
                                <i class="bi bi-gift-fill me-2"></i>¡Invita y gana!
                            </h4>
                            <p class="mb-3" style="color: #8ab8c8;">
                                Comparte tu enlace con amigos y familiares. Cuando se registren y activen su primer plan,
                                ganarás <strong class="text-white">{{ $bonusSessionsConfig }} sesiones extra gratuitas</strong>
                                en tu tratamiento.
                            </p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="input-group" style="max-width: 500px;">
                                    <input type="text" class="form-control bg-dark text-white border-secondary"
                                           id="referralLinkInput" value="{{ $referralLink }}" readonly>
                                    <button class="btn btn-outline-light" type="button" id="copyReferralLinkBtn"
                                            onclick="copyLinkToClipboard()">
                                        <i class="bi bi-clipboard"></i> Copiar
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small style="color: #6a9dac;">
                                    Tu código: <strong class="text-white">{{ $user->referral_code }}</strong>
                                </small>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5 text-center mt-3 mt-lg-0">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="rounded-3 p-3" style="background: rgba(255,255,255,0.05);">
                                        <div class="fs-2 fw-bold text-white">{{ $totalReferred }}</div>
                                        <small style="color: #8ab8c8;">Invitados</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="rounded-3 p-3" style="background: rgba(255,255,255,0.05);">
                                        <div class="fs-2 fw-bold" style="color: #4dd0a1;">{{ $successfulReferrals }}</div>
                                        <small style="color: #8ab8c8;">Exitosos</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="rounded-3 p-3" style="background: rgba(255,255,255,0.05);">
                                        <div class="fs-2 fw-bold" style="color: #f7c948;">{{ $totalBonusSessions }}</div>
                                        <small style="color: #8ab8c8;">Sesiones ganadas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de referidos --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>Mis referidos</h5>
                    @if($pendingReferrals > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingReferrals }} pendiente(s)</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($referrals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Nombre</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Sesiones ganadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referrals as $referral)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-circle fs-4 text-secondary"></i>
                                            <div>
                                                <div class="fw-semibold">{{ $referral->referred->name ?? 'Pendiente' }}</div>
                                                <small class="text-secondary">{{ $referral->referred->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-secondary">{{ $referral->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        @if($referral->status === 'rewarded')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Recompensado</span>
                                        @elseif($referral->status === 'registered')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Registrado</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($referral->status === 'rewarded')
                                            <span class="fw-bold" style="color: #4dd0a1;">+{{ $referral->bonus_sessions }}</span>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-person-hearts fs-1 text-secondary"></i>
                        <p class="mt-3 text-secondary">Aún no has referido a nadie.<br>¡Comparte tu enlace y empieza a ganar sesiones!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                El programa de referidos no está disponible en este momento.
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    function copyLinkToClipboard() {
        const input = document.getElementById('referralLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        const showSuccess = () => {
            const btn = document.getElementById('copyReferralLinkBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> ¡Copiado!';
            btn.classList.remove('btn-outline-light');
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-light');
            }, 2000);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(showSuccess);
        } else {
            try {
                document.execCommand('copy');
                showSuccess();
            } catch (err) {
                console.error('Error al copiar: ', err);
                alert('No se pudo copiar automáticamente. Por favor copia el texto manualmente.');
            }
        }
    }
</script>
@endpush
@endsection
