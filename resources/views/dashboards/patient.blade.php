@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center my-2">
            <h1 class="fw-bold m-0" style="color: #fffffd;">¡{{ __('Hello') }}, {{ Auth::user()->name }}!</h1>
        </div>

        <div class="row justify-content-center my-2">
            <div class="col-12 col-md-8 mb-3">
                <div class="row gx-3 gy-3 my-2">
                    @can('patient_medical_record_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('medical-record.index') }}"
                            >
                                <i class="bi bi-clipboard-heart-fill"></i>
                                {{ __('Medical Record') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_qualify_staff_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('qualify-staff.index') }}"
                            >
                                <i class="bi bi-star-fill"></i>
                                {{ __('Qualify Staff') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_treatment_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="#"
                            >
                                <i class="bi bi-card-checklist"></i>
                                {{ __('Treatment') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_care_tips_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('care-tips.index') }}"
                            >
                                <i class="bi bi-bookmark-star-fill"></i>
                                {{ __('Care Tips') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_buy_package_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('client.treatment.index') }}"
                            >
                                <i class="bi bi-heart-fill"></i>
                                {{ __('Buy Package') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_virtual_wallet_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('virtual-wallet.index') }}"
                            >
                                <i class="bi bi-wallet"></i>
                                {{ __('Virtual Wallet') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_referrals_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('referrals.index') }}"
                            >
                                <i class="bi bi-person-hearts"></i>
                                {{ __('Referrals') }}
                            </a>
                        </div>
                    @endcan
                    @can('patient_recomentations_home_btn')
                        <div class="col-12 col-md-8">
                            <a class="btn btn-custom btn-custom-height d-flex justify-content-start text-start gap-3 fs-4 align-items-center"
                                role="button"
                                href="{{ route('recomentations.index') }}"
                            >
                                <i class="bi bi-person-add"></i>
                                {{ __('Recommendations') }}
                            </a>
                        </div>
                    @endcan
                </div>
                <div class="row gx-3 gy-3 mt-2">
                    @can('patient_schedule_appointment_home_btn')
                        <div class="col-12 col-md-6">
                            <a class="btn btn-custom btn-schedule-appointment d-flex justify-content-start text-start gap-3 align-items-center"
                                role="button"
                                href="{{ route('schedule-appointment.index') }}"
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
            </div>
            <div class="col-12 col-md-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body px-4">
                            <div class="row">
                                <div class="col-3">
                                    <img alt="photo profile" width="68px" height="68px" class="rounded-circle navbar-photo me-2" src="{{asset(Storage::url(Auth::user()->photo_profile?:config('app.app_default_img_profile')))}}">
                                </div>
                                <div class="col text-start">
                                    <p class="fw-bold m-0">{{ __('Next Appointment') }}</p>
                                    <p class="fs-3 fw-bold m-0 text-uppercase" style="color: #b6d3db;">N/A</p>
                                </div>
                            </div><hr>
                            <div class="row">
                                <div class="col text-start">
                                    <p class="fw-bold m-0">{{ __('Balance') }}</p>
                                    <p class="fs-1 fw-bold m-0 text-uppercase" style="color: #f9ffff;">$0.00</p>
                                </div>
                            </div><hr>
                            <div class="row">
                                <div class="col text-start">
                                    <p class="fw-bold m-0">{{ __('Active Packages') }}</p>
                                    <p class="fs-1 fw-bold m-0 text-uppercase" style="color: #b6d3db;">0</p>
                                </div>
                            </div><hr>
                            <div class="row">
                                <div class="col text-start">
                                    <p class="fw-bold m-0">{{ __('Latest recommendations') }}</p>
                                    <ul style="color: #b6d3db;">
                                        <li>Elemento 1</li>
                                        <li>Elemento 2</li>
                                        <li>Elemento 3</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <div class="card shadow">
                        <div class="card-body px-4">
                            <h5 class="card-title fw-bold">{{ __('Treatment Progress') }}</h5>
                            <div class="progress my-4" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar w-75"></div>
                            </div>
                            <p class="fw-bold m-0" style="color: #33a1d6;">{{ __('') }}Progreso oratnicic</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
