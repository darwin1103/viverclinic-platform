@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center my-2">
            <h1 class="fw-bold m-0" style="color: #fffffd;">¡{{ __('Hello') }}, {{ Auth::user()->name }}!</h1>
        </div>

        <div class="row justify-content-center my-2">
            <div class="col-12 mb-3">
                <div class="row gx-3 gy-3 my-2">
                    @can('employee_agenda_day_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height" role="button"
                                href="{{ route('agenda-day.index') }}">
                                {{ __('Agenda of the day') }}
                            </a>
                        </div>
                    @endcan
                    @can('employee_agenda_new_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height" role="button"
                                href="{{ route('agenda-new.index') }}">
                                {{ __('New agenda') }}
                            </a>
                        </div>
                    @endcan
                    @can('employee_job_training_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height" role="button"
                                href="{{ route('job-trailing.index') }}">
                                {{ __('Job Trailing') }}
                            </a>
                        </div>
                    @endcan
                    @can('employee_promotions_home_btn')
                        <div class="col-12 col-md-4">
                            <a class="btn btn-custom btn-custom-height" role="button"
                                href="{{ route('promotions.index') }}">
                                {{ __('Promotions') }}
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

    </div>
@endsection
