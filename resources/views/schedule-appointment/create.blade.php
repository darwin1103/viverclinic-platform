@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>{{ __('Schedule Appointment') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Schedule Appointment') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">{{ __('Module under construction, coming soon') }}.</div>
    </div>
</div>
@endsection
