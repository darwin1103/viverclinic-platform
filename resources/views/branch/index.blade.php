@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>{{ __('Branches') }}</h1>
        </div>
        <div class="col-12 col-md-5 col-lg-4 text-end" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('branch.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;{{ __('Add') }}
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
                                    <th scope="col">{{ __('Created') }}</th>
                                    <th scope="col">{{ __('Updated') }}</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($branches && count($branches) > 0)
                                    @php
                                        $totalItems = 0;
                                    @endphp
                                    @foreach ($branches as $branch)
                                        @php
                                            $totalItems++;
                                        @endphp
                                        <tr>
                                            <th scope="row">{{ $totalItems }}</th>
                                            <td style="min-width: 160px;">{{ $branch->name }}</td>
                                            <td style="min-width: 130px;">{{ $branch->created_at }}</td>
                                            <td style="min-width: 130px;">{{ $branch->updated_at }}</td>
                                            <td style="min-width: 160px;">
                                                <a href="{{ route('branch.edit', $branch->id) }}" class="btn btn-warning me-2"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   data-bs-custom-class="custom-tooltip"
                                                   data-bs-title="Editar">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button class="btn btn-danger" type="button"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Delete')}}"
                                                    onclick="showDeleteConfirmation('{{$branch->id}}')">
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
                        @if (isset($branches))
                            {{ $branches->links('layouts.numbers-pagination') }}
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
        //     const $roleId = $(this).attr('data-role-id');
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
        //                             <input class="form-check-input me-1 add-permission-to-rol" type="checkbox" value="" `+p['contains']+` id="`+p['id']+`" data-role-id="`+$roleId+`">
        //                             <label class="form-check-label stretched-link" for="`+p['id']+`">`+p['name']+`</label>
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
        //     const $roleId = $(this).attr('data-role-id');
        //     const $roleName = $(this).attr('data-role-name');
        //     const $usersURL = $(this).attr('data-users-url');
        //     $.ajax({
        //         url: $usersURL,
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
        //                             <input class="form-check-input me-1 add-user-to-rol" type="checkbox" value="" `+p['contains']+` id="`+p['id']+`" data-role-id="`+$roleId+`">
        //                             <label class="form-check-label stretched-link" for="`+p['id']+`">`+p['name']+`</label>
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
        //     const $roleId = $(this).attr('data-role-id');
        //     const $permissionId = $(this).attr('id');
        //     if ($(this).is(':checked')) {
        //         $.ajax({
        //             url: "{{ route('roles.assign.permission') }}",
        //             method: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 roleId: $roleId,
        //                 permissionId: $permissionId
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
        //                 roleId: $roleId,
        //                 permissionId: $permissionId
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
        //     const $roleId = $(this).attr('data-role-id');
        //     const $userId = $(this).attr('id');
        //     if ($(this).is(':checked')) {
        //         $.ajax({
        //             url: "{{ route('roles.assign.user') }}",
        //             method: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 roleId: $roleId,
        //                 userId: $userId
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
        //                 roleId: $roleId,
        //                 userId: $userId
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
    function showDeleteConfirmation(id) {
        const modal = new bootstrap.Modal('#removeConfirmationModal');
        $('#delete').attr('action','{{url("/branches")}}'+'/'+id);
        $('#deleteElementBtn').attr('action','{{url("/branches")}}'+'/'+id);
        modal.show();
    }
</script>
@endpush
@endsection
