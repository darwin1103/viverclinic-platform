@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>{{ __('Create') }}</h1>
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
                    <h2 class="mb-3">{{__('New User')}}</h2>
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('Name') }}">
                            <label for="name">{{ __('Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating my-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('Email Address') }}">
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating">
                            <select class="form-select @error('roleSelect') is-invalid @enderror" id="roleSelect" name="roleSelect" aria-label="role selector">
                                <option value="" selected>{{ __('Select an item') }}</option>
                                @if ($roles && count($roles) > 0)
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->uuid }}">{{ $role->name }}</option>    
                                    @endforeach
                                @else
                                    <option>{{ __('There are no options available') }}</option>
                                @endif
                            </select>
                            <label for="roleSelect">{{ __('Assign a role to the user') }}</label>
                            @error('roleSelect')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    }, false);
</script>
@endsection