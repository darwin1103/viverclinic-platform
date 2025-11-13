@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/client/appointment/schedule/index/appointments.css') }}">
@endpush

@section('content')

<main class="container">
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
                />
            </div>
        </div>
    </div>
</main>

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
