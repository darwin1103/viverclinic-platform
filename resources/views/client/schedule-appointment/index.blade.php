@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/client/appointment/schedule/index/appointments.css') }}">
@endpush

@section('content')

<div class="container-fluid p-0 py-4">
    <div class="">

        <h1 class="h3">Agendar Cita</h1>

        <div class="card my-4">
            <div class="card-body">
                <x-client.appointment.schedule.index.treatment-header
                    :treatment="$contracted_treatment->treatment"
                    :branch="$contracted_treatment->branch"
                    :attendedCount="$attendedCount"
                    :missedCount="$missedCount"
                    :pendingCount="$pendingCount"
                    :paymentIsUpToDate="$paymentIsUpToDate"
                    :nextPaymentAmount="$nextPaymentAmount"
                    :nextPaymentDescription="$nextPaymentDescription"
                    :contractedTreatmentId="$contracted_treatment->id"
                    :canPayInstallment="$canPayInstallment"
                    :totalRemainingAmount="$totalRemainingAmount"
                    :paymentVerificationPending="$paymentVerificationPending"
                    :lastOrderRejected="$lastOrderRejected"
                    :lastOrderMessage="$lastOrderMessage"
                />

                @if (!$paymentIsUpToDate)
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Debe tener el pago completado para poder agendar una cita.
                    </div>
                @endif

                <hr class="my-4">

                <x-client.appointment.schedule.index.sessions-table
                    :sessions="$sessions"
                    :totalSessions="$totalSessions"
                    :paymentIsUpToDate="$paymentIsUpToDate"
                    :branchId="$contracted_treatment->branch->id"
                    :contractedTreatmentId="$contracted_treatment->id"
                    :nextSessionNumber="$nextSessionNumber"
                    :hasFutureAppointment="$hasFutureAppointment"
                />
            </div>
        </div>
    </div>

    {{-- Aclaraciones al paciente --}}
    <div class="row justify-content-center mt-3">
        <div class="col-12 col-lg-8">
            <div class="alert alert-info border-info" role="alert">
                <h5 class="alert-heading fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i>Atención usuario</h5>
                <ul class="mb-0 text-start">
                    <li>Las citas agendadas se deben confirmar, reagendar o cancelar con 24 horas de anticipación, de lo contrario se tomará como no asistida.</li>
                    <li>Recuerda que no asistir puntual a tu tratamiento va a afectar los resultados del mismo y el centro de estética no se hace responsable.</li>
                    <li>Recuerda asistir a tu cita siguiendo las recomendaciones previas al tratamiento.</li>
                    <li>El tiempo estimado de cada sesión es de 20 minutos.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<x-client.appointment.schedule.index.appointment-modal />
<x-client.appointment.schedule.index.rating-modal />
@endsection

@push('scripts')
<script src="{{ asset('js/client/appointment/schedule/index/calendar.js') }}"></script>
<script src="{{ asset('js/client/appointment/schedule/index/rating.js') }}"></script>
<script src="{{ asset('js/client/appointment/schedule/index/sessions-table.js') }}"></script>
<script>
    // Initialize sessions data from backend
    window.sessionsData = @json($sessions);
    window.totalSessions = {{ $totalSessions }};
    window.paymentIsUpToDate = {{ $paymentIsUpToDate ? 'true' : 'false' }};

    document.addEventListener('DOMContentLoaded', function() {
        initializeSessionsTable();
    });
</script>
@endpush
