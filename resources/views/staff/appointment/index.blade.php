@extends('layouts.employee')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">

            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="h3">Mis Citas Asignadas</h1>
                </div>
            </div>

            {{-- Filters Component --}}
            {{-- Se asume que el controlador pasa la variable $treatments --}}
            <x-staff.appointments.filters :treatments="$treatments ?? []" />

            {{-- Appointments Table Component --}}
            {{-- Se asume que el controlador pasa la variable $appointments --}}
            <x-staff.appointments.table :appointments="$appointments ?? []" />

        </div>
    </div>
</div>

{{-- Action Modal Component --}}
<x-staff.appointments.modal />
@endsection

@push('scripts')
    {{-- Link to the specific JavaScript file for this view --}}
    <script src="{{ asset('js/staff/appointments.js') }}"></script>
@endpush
