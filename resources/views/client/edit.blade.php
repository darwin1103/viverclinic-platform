@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>{{ __('Edit') }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card" style="width: 30rem;">
                <div class="card-body m-0 m-lg-3">
                    <h2 class="mb-3">{{__('Edit User')}}</h2>
                    <form action="{{ route('client.update', ['client' => $client]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('Name') }}" value="{{ $client->name ?? '' }}">
                            <label for="name">{{ __('Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating my-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('Email Address') }}" value="{{ $client->email ?? '' }}">
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requestInformedConsent" name="requestInformedConsent" @if($client->informed_consent) checked @endif>
                            <label class="form-check-label" for="requestInformedConsent">
                                {{ __('Request Informed Consent') }}
                            </label>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
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
