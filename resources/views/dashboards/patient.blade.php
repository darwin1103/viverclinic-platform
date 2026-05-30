@extends('layouts.app')
@section('content')
    <div class="container-fluid p-0">
        <div class="row justify-content-center my-2">
            <h1 class="fw-bold m-0" style="color: #fffffd;">¡{{ __('Hello') }}, {{ Auth::user()->name }}!</h1>
        </div>

        @if(isset($hasBlockedScheduling) && $hasBlockedScheduling)
            <div class="row justify-content-center my-3 px-2">
                <div class="col-12">
                    <div class="alert alert-info border-info d-flex align-items-center gap-3 p-3 shadow-sm mb-2" style="border-radius: 12px; background-color: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1 text-info">Pago pendiente / Abono incompleto</h5>
                            <p class="mb-0 text-white-50 small">
                                Tienes tratamientos con saldos pendientes. Recuerda completar tus abonos o cuotas para poder iniciar tu tratamiento y agendar tus citas.
                            </p>
                            <div class="mt-2 d-flex flex-wrap gap-1">
                                @foreach($blockedTreatments as $bt)
                                    <span class="badge bg-info text-dark">
                                        {{ $bt->treatment->name ?? 'Tratamiento' }} (Saldo: ${{ number_format($bt->remainingBalance(), 0, ',', '.') }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mobile only: Next Appointment and Treatment Progress -->
        <div class="row d-lg-none my-2 px-2">
            <!-- Next Appointment Card -->
            <div class="col-12 mb-3">
                <div class="card shadow" style="border: 1px solid rgba(255, 255, 255, 0.06); background: var(--vc-card, #0f2a30);">
                    <div class="card-body px-4 py-3">
                        <div class="row align-items-center">
                            <div class="col-3 col-sm-2 text-center">
                                <img alt="photo profile" width="55px" height="55px" class="rounded-circle navbar-photo" src="{{ Auth::user()->photo_profile ? asset(Storage::url(Auth::user()->photo_profile)) : asset('images/icons/default-avatar.svg') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=55&background=6c757d&color=fff'">
                            </div>
                            <div class="col text-start">
                                <p class="fw-bold m-0" style="font-size: 0.9rem; color: #fffffd;">{{ __('Next Appointment') }}</p>
                                <p class="{{ $nextAppointment ? 'fs-5 text-uppercase' : 'small fw-normal' }} fw-bold m-0" style="color: #b6d3db;">
                                    {{ $nextAppointment ? \Carbon\Carbon::parse($nextAppointment->schedule)->isoFormat('D MMM YYYY, h:mm a') : 'No tienes citas programadas' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Treatment Progress Card -->
            <div class="col-12 mb-3">
                <div class="card shadow" style="border: 1px solid rgba(255, 255, 255, 0.06); background: var(--vc-card, #0f2a30);">
                    <div class="card-body px-4 py-3">
                        <h6 class="card-title fw-bold m-0 mb-2" style="font-size: 0.9rem; color: #fffffd;">{{ __('Treatment Progress') }}</h6>
                        <div class="progress mb-2" role="progressbar" style="height: 12px;" aria-valuenow="{{ $treatmentProgress }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{ $treatmentProgress }}%"></div>
                        </div>
                        <p class="fw-bold m-0 small" style="color: #33a1d6;">{{ $treatmentProgress }}% - {{ $treatmentName }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-2">
            <div class="col-12 col-lg-8 mb-3">
                <div class="row gx-3 gy-3 my-2">
                    @can('patient_medical_record_home_btn')
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('medical-record.index') }}"
                            >
                                <img src="{{ asset('images/icons/historia-clinica.png') }}" width="60" height="60" alt="Icon">
                                {{ __('Medical Record') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_treatment_home_btn')
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('client.contracted-treatment.index') }}"
                            >
                                <img src="{{ asset('images/icons/Control-tratamiento.png') }}" width="60" height="60" alt="Icon">
                                Control de tratamientos
                            </a>
                        </div>
                    @endcan
                    @can('patient_care_tips_home_btn')
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('care-tips.index') }}"
                            >
                                <img src="{{ asset('images/icons/Tips-cuidados.png') }}" width="60" height="60" alt="Icon">
                                {{ __('Care Tips') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_care_tips_home_btn')
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('client.shop.index') }}"
                            >
                                <img src="{{ asset('images/icons/Tienda.png') }}" width="60" height="60" alt="Icon">
                                Tienda
                            </a>
                        </div>
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('client.orders.index') }}"
                            >
                                <img src="{{ asset('images/icons/Compras.png') }}" width="60" height="60" alt="Icon">
                                Compras
                            </a>
                        </div>
                    @endcan
                    @can('patient_buy_package_home_btn')
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('client.treatment.index') }}"
                            >
                                <img src="{{ asset('images/icons/Comprar-paquetes.png') }}" width="60" height="60" alt="Icon">
                                {{ __('Buy Package') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_referrals_home_btn')
                        <div class="col-12 col-lg-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('referrals.index') }}"
                            >
                                <img src="{{ asset('images/icons/Referidos.png') }}" width="60" height="60" alt="Icon">
                                {{ __('Referrals') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_recomentations_home_btn')
                        <div class="col-12 col-lg-8">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('recomentations.index') }}"
                            >
                                <img src="{{ asset('images/icons/Recomendaciones.png') }}" width="60" height="60" alt="Icon">
                                {{ __('Recommendations') }}
                            </a>
                        </div>
                    @endcan
                </div>
                @if($createAppointmentUrl)
                    <div class="row gx-3 gy-3 mt-2">
                        @can('patient_schedule_appointment_home_btn')
                            <div class="col-12 col-md-6">
                                <a class="btn btn-custom btn-schedule-appointment d-flex justify-content-start text-start gap-3 align-items-center"
                                    role="button"
                                    href="{{ $createAppointmentUrl }}"
                                >
                                    <i class="bi bi-plus-circle-fill"></i>
                                    {{ __('Schedule an Appointment') }}
                                </a>
                            </div>
                        @endcan
                        @can('patient_cancel_appointment_home_btn')
                            <div class="col-12 col-md-6">
                                <a class="btn btn-custom btn-cancel-appointment d-flex justify-content-start text-start gap-3 align-items-center"
                                    role="button"
                                    href="{{ route('cancel-appointment.index') }}"
                                >
                                    <i class="bi bi-x-circle-fill"></i>
                                    {{ __('Cancel Appointment') }}
                                </a>
                            </div>
                        @endcan
                    </div>
                @endif

            </div>
            <div class="col-12 col-lg-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body px-4">
                            <div class="d-none d-lg-block">
                                <div class="row">
                                    <div class="col-3">
                                        <img alt="photo profile" width="68px" height="68px" class="rounded-circle navbar-photo me-2" src="{{ Auth::user()->photo_profile ? asset(Storage::url(Auth::user()->photo_profile)) : asset('images/icons/default-avatar.svg') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=68&background=6c757d&color=fff'">
                                    </div>
                                    <div class="col text-start">
                                        <p class="fw-bold m-0">{{ __('Next Appointment') }}</p>
                                        <p class="{{ $nextAppointment ? 'fs-4 text-uppercase' : 'small fw-normal' }} fw-bold m-0" style="color: #b6d3db;">
                                            {{ $nextAppointment ? \Carbon\Carbon::parse($nextAppointment->schedule)->isoFormat('D MMM YYYY, h:mm a') : 'No tienes citas programadas' }}
                                        </p>
                                    </div>
                                </div><hr>
                            </div>
                            <div class="row">
                                <div class="col text-start">
                                    <p class="fw-bold m-0">{{ __('Active Packages') }}</p>
                                    <p class="fs-1 fw-bold m-0 text-uppercase" style="color: #b6d3db;">{{ $activePackagesCount }}</p>
                                </div>
                            </div><hr>
                            @if($pendingBalance > 0)
                            <div class="row">
                                <div class="col text-start">
                                    <p class="fw-bold m-0 text-info">Saldo por pagar</p>
                                    <p class="fs-1 fw-bold m-0 text-uppercase text-info">${{ number_format($pendingBalance, 0, ',', '.') }}</p>
                                </div>
                            </div><hr>
                            @endif
                            <div class="row">
                                <div class="col text-start">
                                    <p class="fw-bold m-0">{{ __('Latest recommendations') }}</p>
                                    <ul style="color: #b6d3db;">
                                        @forelse($latestRecommendations as $recommendation)
                                            <li>{{ $recommendation->title }}</li>
                                        @empty
                                            <li>Ninguna recomendación reciente</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3 d-none d-lg-block">
                    <div class="card shadow">
                        <div class="card-body px-4">
                            <h5 class="card-title fw-bold">{{ __('Treatment Progress') }}</h5>
                            <div class="progress my-4" role="progressbar" aria-label="Basic example" aria-valuenow="{{ $treatmentProgress }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: {{ $treatmentProgress }}%"></div>
                            </div>
                            <p class="fw-bold m-0" style="color: #33a1d6;">{{ $treatmentProgress }}% - {{ $treatmentName }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@if(session('show_welcome_popup'))
    <!-- Welcome Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 1px solid rgba(255,255,255,.06); background-color: var(--vc-card, #0f2a30);">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0 pb-4 px-4">
                    <div class="mb-4 text-primary" style="color: var(--vc-primary) !important;">
                        <i class="bi bi-stars" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-3" id="welcomeModalLabel">¡Bienvenido(a) a ViverClinic!</h3>
                    <h5 class="text-white mb-3">{{ Auth::user()->name }}</h5>
                    <p class="text-secondary fs-5 mb-4">Estamos encantados de tenerte de vuelta. Nuestro equipo está listo para brindarte la mejor experiencia en cuidado y bienestar. ¡Explora tu panel para descubrir todo lo que hemos preparado para ti!</p>
                    <button type="button" class="btn btn-primary btn-lg w-100 rounded-pill" data-bs-dismiss="modal">Explorar mi panel</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var welcomeModalEl = document.getElementById('welcomeModal');
                if (welcomeModalEl) {
                    var welcomeModal = new bootstrap.Modal(welcomeModalEl, {
                        backdrop: 'static'
                    });
                    welcomeModal.show();
                    
                    // Auto-close after 15 seconds if they don't click anything
                    setTimeout(function() {
                        if (document.getElementById('welcomeModal').classList.contains('show')) {
                            welcomeModal.hide();
                        }
                    }, 15000);
                }
            });
        </script>
        {{ session()->forget('show_welcome_popup') }}
    @endpush
@endif
