@extends('layouts.admin')
@section('content')
<div class="container">
    {{-- HEADER --}}
    <div class="row mb-3">
        <div class="col-12 col-lg-5">
            <h1>Clientes</h1>
        </div>
        <div class="col-12 col-lg-7 d-flex flex-wrap justify-content-lg-end align-items-center gap-2">
            <a class="btn btn-primary" href="{{ route('client.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo cliente
            </a>
        </div>
    </div>

    {{-- SECCIÓN DE FILTROS --}}
    <x-admin.client.client-filter />

    {{-- TABLA DE CLIENTES --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Fecha de registro</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clients as $key => $client)
                                    <tr>
                                        <td style="min-width: 180px;">{{ $client->name }}</td>
                                        <td style="min-width: 150px;">{{ $client->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="min-width: 140px;">
                                            <span class="badge bg-info text-dark">
                                                {{ $client->patientProfile?->branch?->name ?? 'No asignada' }}
                                            </span>
                                        </td>
                                        <td style="min-width: 160px;">
                                            <a class="mx-2" href="{{ route('client.show', $client) }}"
                                               data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Ver">
                                               <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a class="mx-2" href="{{ route('client.edit', $client) }}"
                                               data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Editar">
                                               <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" type="button"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Eliminar"
                                                    onclick="showDeleteConfirmation('{{ $client->id }}')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            No se encontraron registros con los filtros aplicados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- PAGINACIÓN --}}
                        @if ($clients->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $clients->links('layouts.numbers-pagination') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('common.deleteConfirmationModal')
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/client/index/showDeleteConfirmation.js') }}"></script>
    <script src="{{ asset('js/admin/client/index/filter.js') }}"></script>
@endpush

