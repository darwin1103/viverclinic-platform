@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/appointments.css') }}">
@endpush

@section('content')
<!-- Main Content -->
<main class="">
    <div class="container-fluid">

        <!-- Toolbar Component -->
        <x-admin.appointment.toolbar />

        <!-- Calendar View Component -->
        <x-admin.appointment.calendar />
    </div>
</main>

<!-- Modal Components -->
<x-admin.appointment.detail-modal />
<x-admin.appointment.range-modal />
@endsection

@push('scripts')
<script>
    // Pass data to JavaScript
    window.apiEndpoints = {
        fetchAppointments: '{{ route('admin.appointments.fetch') }}',
        confirmAppointment: '{{ route('admin.appointments.confirm', ['appointment' => ':id']) }}',
        markAsAttended: '{{ route('admin.appointments.mark-attended', ['appointment' => ':id']) }}',
        assignStaff: '{{ route('admin.appointments.assign-staff', ['appointment' => ':id']) }}',
        reschedule: '{{ route('admin.appointments.reschedule', ['appointment' => ':id']) }}',
        cancel: '{{ route('admin.appointments.cancel', ['appointment' => ':id']) }}',
        getStaffList: '{{ route('admin.staff.list') }}',
        getTreatmentsList: '{{ route('admin.treatments.list') }}'
    };
</script>
<script src="{{ asset('js/admin/appointments/calendar.js') }}"></script>
<script src="{{ asset('js/admin/appointments/actions.js') }}"></script>
<script src="{{ asset('js/admin/appointments/filters.js') }}"></script>
@endpush
