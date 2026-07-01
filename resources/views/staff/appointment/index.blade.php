@extends('layouts.employee')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="h3">Mis Citas Asignadas</h1>
                </div>
            </div>

            {{-- Tabs de Navegación --}}
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'today' ? 'active fw-bold' : '' }}" href="{{ route('staff.appointment.index', ['tab' => 'today']) }}">
                        <i class="bi bi-calendar-day me-2"></i>Agenda de Hoy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'archive' ? 'active fw-bold' : '' }}" href="{{ route('staff.appointment.index', ['tab' => 'archive']) }}">
                        <i class="bi bi-archive me-2"></i>Historial de Citas
                    </a>
                </li>
            </ul>

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
    <script src="{{ asset('js/staff/appointments.js') }}?v={{ time() }}"></script>
@endpush
