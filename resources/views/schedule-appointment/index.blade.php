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
</div>

<div class="container mt-4">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">

                    <div class="row d-flex justify-content-center align-items-center mb-4">
                        <div class="col-12 col-lg-6 text-center">
                            {{-- Puedes poner un logo aquí si lo deseas --}}
                            <h2 class="h3 fw-bold">Control de Tratamiento</h2>
                        </div>
                    </div>

                    {{-- Treatment Info Section --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="card-title">{{ $treatment->name }}</h5>
                            <p class="card-text mb-1"><strong>Especialista:</strong> {{ $specialist->name }}</p>
                            <p class="card-text">
                                <strong>Sucursal:</strong> {{ $branch->name }}
                                (<a href="{{ $branch->google_maps_link }}" target="_blank">Ver en mapa</a>)
                            </p>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end align-items-center">
                             <div class="text-start text-md-end">
                                <span class="badge bg-success me-2 p-2">Asistidas: {{ $attendedCount }}</span>
                                <span class="badge bg-danger me-2 p-2">Perdidas: {{ $missedCount }}</span>
                                <span class="badge bg-secondary p-2">Pendientes: {{ $pendingCount }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Status Warning --}}
                    @if (!$paymentIsUpToDate)
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Debe tener el pago completado para poder agendar una cita.
                        </div>
                    @endif

                    {{-- Blade Component for the sessions table --}}
                    <x-appointment.schedule.index.sessions-table
                        :sessions="$sessions"
                        :totalSessions="$totalSessions"
                        :paymentIsUpToDate="$paymentIsUpToDate"
                    />

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Blade Component for the modal --}}
<x-appointment.schedule.index.appointment-modal />
@endsection

@push('scripts')
<script>
    // Pure JavaScript to handle modal data
    document.addEventListener('DOMContentLoaded', function () {
        const scheduleModal = document.getElementById('scheduleAppointmentModal');
        if (scheduleModal) {
            scheduleModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;

                // Extract info from data-bs-* attributes
                const sessionNumber = button.getAttribute('data-bs-session-number');
                const currentAppointmentDate = button.getAttribute('data-bs-date');

                // Update the modal's content.
                const modalTitle = scheduleModal.querySelector('.modal-title');
                const modalSessionNumberInput = scheduleModal.querySelector('#modalSessionNumber');
                const modalDateInput = scheduleModal.querySelector('#appointmentDate');
                const modalSessionNumberTitle = scheduleModal.querySelector('#modalSessionNumberTitle');

                modalSessionNumberTitle.textContent = sessionNumber;
                modalSessionNumberInput.value = sessionNumber;

                // Set min date to today
                const today = new Date().toISOString().split('T')[0];
                modalDateInput.setAttribute('min', today);

                // If rescheduling, pre-fill the date
                if(currentAppointmentDate) {
                    modalDateInput.value = currentAppointmentDate;
                } else {
                    modalDateInput.value = '';
                }
            });
        }
    });
</script>
@endpush
