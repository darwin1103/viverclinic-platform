@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Clientes</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-end" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('client.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo cliente
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">Roles</th>
                                    <th scope="col">{{ __('Created') }}</th>
                                    <th scope="col">{{ __('Updated') }}</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($clients && count($clients) > 0)
                                    @php
                                        $totalItems = 0;
                                    @endphp
                                    @foreach ($clients as $client)
                                        @php
                                            $totalItems++;
                                        @endphp
                                        <tr>
                                            <th scope="row">{{ $totalItems }}</th>
                                            <td style="min-width: 160px;">{{ $client->name }}</td>
                                            <td style="min-width: 160px;">
                                                @if($client->roles->count() > 0)
                                                    @foreach($client->roles as $role)
                                                        <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Sin rol asignado</span>
                                                @endif
                                            </td>
                                            <td style="min-width: 130px;">{{ $client->created_at }}</td>
                                            <td style="min-width: 130px;">{{ $client->updated_at }}</td>
                                            <td style="min-width: 160px;">
                                                <a class="mx-2"
                                                    href="{{ route('client.show', $client) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Show')}}"><i class="bi bi-eye-fill"></i></a>
                                                <a class="mx-2"
                                                    href="{{ route('client.edit', $client) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Edit')}}"><i class="bi bi-pencil-square"></i></a>
                                                <button class="btn btn-danger" type="button"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Delete')}}"
                                                    onclick="showDeleteConfirmation('{{$client->id}}')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            {{ __('There are no records') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        @if (isset($clients))
                            {{ $clients->links('layouts.numbers-pagination') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="modal fade" id="addPermissionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addPermissionsModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="addUsersToRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addUsersToRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addUsersToRoleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div> --}}
@include('common.deleteConfirmationModal')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // $(document).on('click','.add-permissions',function(){
        //     const $roleUUId = $(this).attr('data-role-uuid');
        //     const $roleName = $(this).attr('data-role-name');
        //     const $permissionURL = $(this).attr('data-permission-url');
        //     $.ajax({
        //         url: $permissionURL,
        //         method: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //             if (data.permissions.length>0) {
        //                 $('#addPermissionsModal .modal-body').empty();
        //                 $('#addPermissionsModal .modal-body').append(`
        //                     <ul class="list-group list-group-flush"></ul>
        //                 `);
        //                 data.permissions.forEach((p, index) => {
        //                     $('#addPermissionsModal .modal-body .list-group.list-group-flush').append(`
        //                         <li class="list-group-item">
        //                             <input class="form-check-input me-1 add-permission-to-rol" type="checkbox" value="" `+p['contains']+` id="`+p['uuid']+`" data-role-uuid="`+$roleUUId+`">
        //                             <label class="form-check-label stretched-link" for="`+p['uuid']+`">`+p['name']+`</label>
        //                         </li>
        //                     `);
        //                 });
        //             } else {
        //                 $('#addPermissionsModal .modal-body').html(`
        //                     <p>{{ __('There are no records') }}</p>
        //                 `);
        //             }
        //             $('#addPermissionsModal .modal-header .modal-title').empty();
        //             $('#addPermissionsModal .modal-header .modal-title').html(`
        //                 {{ __('Add Permissions to') }}&nbsp;`+$roleName+`
        //             `);
        //             const modal = new bootstrap.Modal('#addPermissionsModal');
        //             modal.show();
        //         },
        //         error: function(error) {
        //             ajaxErrorHandle(error);
        //         }
        //     });
        // });
        // $(document).on('click','.add-users-to-role',function(){
        //     const $roleUUId = $(this).attr('data-role-uuid');
        //     const $roleName = $(this).attr('data-role-name');
        //     const $clientsURL = $(this).attr('data-users-url');
        //     $.ajax({
        //         url: $clientsURL,
        //         method: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //             if (data.users.length>0) {
        //                 $('#addUsersToRoleModal .modal-body').empty();
        //                 $('#addUsersToRoleModal .modal-body').append(`
        //                     <ul class="list-group list-group-flush"></ul>
        //                 `);
        //                 data.users.forEach((p, index) => {
        //                     $('#addUsersToRoleModal .modal-body .list-group.list-group-flush').append(`
        //                         <li class="list-group-item">
        //                             <input class="form-check-input me-1 add-user-to-rol" type="checkbox" value="" `+p['contains']+` id="`+p['uuid']+`" data-role-uuid="`+$roleUUId+`">
        //                             <label class="form-check-label stretched-link" for="`+p['uuid']+`">`+p['name']+`</label>
        //                         </li>
        //                     `);
        //                 });
        //             } else {
        //                 $('#addUsersToRoleModal .modal-body').html(`
        //                     <p>{{ __('There are no records') }}</p>
        //                 `);
        //             }
        //             $('#addUsersToRoleModal .modal-header .modal-title').empty();
        //             $('#addUsersToRoleModal .modal-header .modal-title').html(`
        //                 {{ __('Add users to') }}&nbsp;`+$roleName+`
        //             `);
        //             const modal = new bootstrap.Modal('#addUsersToRoleModal');
        //             modal.show();
        //         },
        //         error: function(error) {
        //             ajaxErrorHandle(error);
        //         }
        //     });
        // });
        // $(document).on('change','.add-permission-to-rol',function(){
        //     const $roleUUId = $(this).attr('data-role-uuid');
        //     const $permissionUUId = $(this).attr('id');
        //     if ($(this).is(':checked')) {
        //         $.ajax({
        //             url: "{{ route('roles.assign.permission') }}",
        //             method: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 roleUUId: $roleUUId,
        //                 permissionUUId: $permissionUUId
        //             },
        //             success: function(data) {
        //                 iziToast.success({
        //                     message: "{{ __('Successful operation') }}"
        //                 });
        //             },
        //             error: function(error) {
        //                 ajaxErrorHandle(error);
        //             }
        //         });
        //     } else {
        //         $.ajax({
        //             url: "{{ route('roles.remove.permission') }}",
        //             method: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 roleUUId: $roleUUId,
        //                 permissionUUId: $permissionUUId
        //             },
        //             success: function(data) {
        //                 iziToast.success({
        //                     message: "{{ __('Successful operation') }}"
        //                 });
        //             },
        //             error: function(error) {
        //                 ajaxErrorHandle(error);
        //             }
        //         });
        //     }
        // });
        // $(document).on('change','.add-user-to-rol',function(){
        //     const $roleUUId = $(this).attr('data-role-uuid');
        //     const $clientUUId = $(this).attr('id');
        //     if ($(this).is(':checked')) {
        //         $.ajax({
        //             url: "{{ route('roles.assign.user') }}",
        //             method: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 roleUUId: $roleUUId,
        //                 userUUId: $clientUUId
        //             },
        //             success: function(data) {
        //                 iziToast.success({
        //                     message: "{{ __('Successful operation') }}"
        //                 });
        //             },
        //             error: function(error) {
        //                 ajaxErrorHandle(error);
        //             }
        //         });
        //     } else {
        //         $.ajax({
        //             url: "{{ route('roles.remove.user') }}",
        //             method: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 roleUUId: $roleUUId,
        //                 userUUId: $clientUUId
        //             },
        //             success: function(data) {
        //                 iziToast.success({
        //                     message: "{{ __('Successful operation') }}"
        //                 });
        //             },
        //             error: function(error) {
        //                 ajaxErrorHandle(error);
        //             }
        //         });
        //     }
        // });
    }, false);
    function showDeleteConfirmation(clientId) {
        const modal = new bootstrap.Modal('#removeConfirmationModal');
        $('#delete').attr('action','{{url("/client")}}'+'/'+clientId);
        $('#deleteElementBtn').attr('action','{{url("/client")}}'+'/'+clientId);
        modal.show();
    }
</script>
@endpush
@endsection
