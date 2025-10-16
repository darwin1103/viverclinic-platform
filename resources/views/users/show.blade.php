@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>{{ __('Show') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('Users') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('User') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card" style="width: 30rem;">
                <div class="card-body m-0 m-lg-3">
                    <h2 class="mb-3">{{__('Show User')}}</h2>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" value="{{ $user->name ?? '' }}" disabled>
                        <label for="name">{{ __('Name') }}</label>
                    </div>
                    <div class="form-floating my-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email Address') }}" value="{{ $user->email ?? '' }}" disabled>
                        <label for="email">{{ __('Email Address') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="requestInformedConsent" name="requestInformedConsent" @if($user->informed_consent) checked @endif disabled>
                        <label class="form-check-label" for="requestInformedConsent">
                            {{ __('Request Informed Consent') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    }, false);
</script>
@endpush
@endsection