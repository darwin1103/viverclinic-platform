{{-- Asumiendo que tienes un layout principal para el dashboard --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Tratamientos</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a href="{{ route('admin.treatment.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Tratamiento</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- SECCIÓN DE FILTROS --}}
            <div class="card mb-3">
                <div class="card-body py-2">
                    <form action="{{ route('admin.treatment.index') }}" method="GET" id="filter-form" class="row g-2 align-items-end">
                        {{-- Campo de búsqueda por nombre --}}
                        <div class="col-12 col-md-5">
                            <label for="search" class="form-label small mb-1">Buscar por nombre de tratamiento</label>
                            <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Ej: Reducción" value="{{ request('search') }}">
                        </div>

                        {{-- Selector de estado --}}
                        <div class="col-6 col-md-2">
                            <label for="active" class="form-label small mb-1">Estado</label>
                            <select class="form-select form-select-sm" id="active" name="active">
                                <option value="all" @selected(request('active') == 'all')>Todos</option>
                                <option value="active" @selected(request('active') == 'active')>Activo</option>
                                <option value="inactive" @selected(request('active') == 'inactive')>Inactivo</option>
                            </select>
                        </div>

                        {{-- Campo oculto para el filtro de sucursal (controlado por JS) --}}
                        <input type="hidden" name="branch_id" id="branch_id_filter" value="{{ request('branch_id') }}">

                        {{-- Botones --}}
                        <div class="col-12 col-md-1 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                            <a href="{{ route('admin.treatment.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABLA DE RESULTADOS --}}
            <div class="card">

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Sucursales</th>
                                    <th>Sesiones</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($treatments as $treatment)
                                    <tr>
                                        <td>
                                            @if($treatment->main_image)
                                                <img src="{{ Storage::url($treatment->main_image) }}" alt="{{ $treatment->name }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary bg-opacity-25 rounded d-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-image text-secondary"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $treatment->name }}</td>
                                        <td>
                                            @php
                                                $uniqueBranches = $treatment->packages->pluck('branch')->unique('id')->sortBy('name');
                                            @endphp

                                            @if ($uniqueBranches->isNotEmpty())
                                                <div><span class="badge bg-info text-dark">{!! $uniqueBranches->pluck('name')->implode('<br>') !!}</span></div>
                                            @else
                                                <span class="badge bg-secondary">Ninguna</span>
                                            @endif
                                        </td>


                                        <td>{{ $treatment->sessions }}</td>
                                        <td>
                                            @if ($treatment->active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.treatment.edit', $treatment) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.treatment.destroy', $treatment) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tratamiento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No se encontraron tratamientos con los filtros aplicados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $treatments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/treatment/index.js') }}"></script>
@endpush
