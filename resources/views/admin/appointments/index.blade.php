@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/appointments.css') }}?v={{ filemtime(public_path('css/admin/appointments.css')) }}">
@endpush

@section('content')
<!-- Main Content -->
<main class="">
    <div class="container-fluid">

        <!-- Toolbar Component -->
        <x-admin.appointment.toolbar />

        <!-- Staff Status Board -->
        <div class="card mb-4 mt-3" style="border: 1px solid var(--border-color); border-radius: var(--border-radius); background: var(--card-bg);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-color); padding: 10px 15px;">
                <h6 class="m-0" style="color: var(--text-color); font-weight: 600;"><i class="fas fa-users-cog me-2"></i> Estado del Personal (En Vivo)</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-staff-status" title="Actualizar Estado">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="card-body p-3">
                <div id="staff-status-container" class="row g-3">
                    <div class="col-12 text-center text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div> Cargando estado...
                    </div>
                </div>
            </div>
        </div>

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
    window.userCanEditStatus = {{ auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER', 'ADMIN']) ? 'true' : 'false' }};
    window.userCanReassignStaff = {{ auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER']) ? 'true' : 'false' }};
    window.selectedBranchID = '{{ $selectedBranchID }}';
    window.apiEndpoints = {
        fetchAppointments: '{{ route('admin.appointments.fetch') }}',
        confirmAppointment: '{{ route('admin.appointments.confirm', ['appointment' => ':id']) }}',
        markAsAttended: '{{ route('admin.appointments.mark-attended', ['appointment' => ':id']) }}',
        assignStaff: '{{ route('admin.appointments.assign-staff', ['appointment' => ':id']) }}',
        reassignStaff: '{{ route('admin.appointments.reassign-staff', ['appointment' => ':id']) }}',
        reschedule: '{{ route('admin.appointments.reschedule', ['appointment' => ':id']) }}',
        cancel: '{{ route('admin.appointments.cancel', ['appointment' => ':id']) }}',
        updateStatus: '{{ route('admin.appointments.update-status', ['appointment' => ':id']) }}',
        getStaffList: '{{ route('admin.staff.list') }}',
        getTreatmentsList: '{{ route('admin.treatments.list') }}',
        getStaffStatus: '{{ route('admin.appointments.staff-status') }}'
    };
</script>
<script src="{{ asset('js/admin/appointments/calendar.js') }}?v={{ filemtime(public_path('js/admin/appointments/calendar.js')) }}"></script>
<script src="{{ asset('js/admin/appointments/actions.js') }}?v={{ filemtime(public_path('js/admin/appointments/actions.js')) }}"></script>
<script src="{{ asset('js/admin/appointments/filters.js') }}?v={{ filemtime(public_path('js/admin/appointments/filters.js')) }}"></script>
@endpush
