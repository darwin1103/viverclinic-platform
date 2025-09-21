@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center my-2">
        <h1 class="fw-bold m-0" style="color: #fffffd;">¡{{ __('Hello') }}, {{ Auth::user()->name }}!</h1>
    </div>
    @can('ADMIN_DASHBOARD')
        @include('dashboards.admin')
    @endcan
    @can('EMPLOYEE_DASHBOARD')
        @include('dashboards.employee')
    @endcan
    @can('PATIENT_DASHBOARD')
        @include('dashboards.patient')
    @endcan
</div>
@endsection
