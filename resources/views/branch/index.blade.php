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
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">Enlace de registro</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($branches && count($branches) > 0)
                                    @foreach ($branches as $branch)
                                        <tr>
                                            <td style="min-width: 160px;">{{ $branch->name }}</td>
                                            <td style="min-width: 130px;" class="copy-url-cell">
                                                {{ route('registration-by-branch.create', ['branch' => $branch->slug]) }}
                                            </td>
                                            <td style="min-width: 160px;">
                                                <a href="{{ route('branch.edit', $branch->slug) }}" class="btn btn-warning me-2"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   data-bs-custom-class="custom-tooltip"
                                                   data-bs-title="Editar">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button class="btn btn-danger" type="button"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Delete')}}"
                                                    onclick="showDeleteConfirmation('{{$branch->slug}}')">
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
<script src="{{ asset('js/admin/branch/index/copy-url.js') }}"></script>
<script>
    function showDeleteConfirmation(id) {
        const modal = new bootstrap.Modal('#removeConfirmationModal');
        $('#delete').attr('action','{{url("/branches")}}'+'/'+id);
        $('#deleteElementBtn').attr('action','{{url("/branches")}}'+'/'+id);
        modal.show();
    }
</script>
@endpush
@endsection
