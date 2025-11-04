@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Propietarios</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-end" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('owner.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo usuario propietario
            </a>
        </div>
    </div>

    {{-- SECCIÓN DE FILTROS --}}
    <x-admin.owner.index.filter />

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Created') }}</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($owners && count($owners) > 0)
                                    @foreach ($owners as $owner)
                                        <tr>
                                            <td style="min-width: 160px;">{{ $owner->name }}</td>
                                            <td style="min-width: 130px;">{{ $owner->created_at }}</td><td style="min-width: 140px;">
                                            <span class="badge bg-info text-dark">
                                                {{ $owner->ownerProfile?->branch?->name ?? 'No asignada' }}
                                            </span>
                                        </td>
                                            <td style="min-width: 160px;">
                                                <a class="mx-2"
                                                    href="{{ route('owner.show', $owner) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Show')}}"><i class="bi bi-eye-fill"></i></a>
                                                <a class="mx-2"
                                                    href="{{ route('owner.edit', $owner) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Edit')}}"><i class="bi bi-pencil-square"></i></a>
                                                <button class="btn btn-danger" type="button"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{__('Delete')}}"
                                                    onclick="showDeleteConfirmation('{{$owner->id}}', '{{url("/owner")}}')">
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
                        @if (isset($owners))
                            {{ $owners->links('layouts.numbers-pagination') }}
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

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/owner/index/showDeleteConfirmation.js') }}"></script>
    <script src="{{ asset('js/admin/owner/index/filter.js') }}"></script>
@endpush
