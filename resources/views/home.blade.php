@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center my-2">
        <h1 class="fw-bold m-0" style="color: #fffffd;">¡{{ __('Hello') }}, {{ Auth::user()->name }}!</h1>
    </div>
    @if (Auth::user()->getAllPermissions()->isEmpty() && Auth::user()->roles->isEmpty())
        @include('layouts.no-permissions-assigned')
    @else
        @can('admin_dashboard')
            @include('dashboards.admin')
        @endcan
        @can('employee_dashboard')
            @include('dashboards.employee')
        @endcan
        @can('patient_dashboard')
            @include('dashboards.patient')
        @endcan
    @endif
</div>
@endsection
