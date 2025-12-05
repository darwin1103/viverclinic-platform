@extends('layouts.admin')

@section('content')
<div class="container">
        <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Activos</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-end" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.assets.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo activo
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- Filtros (Estilo solicitado) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form id="filter-form" onsubmit="return false;"> <!-- Evita submit tradicional -->
                        <div class="row g-3 align-items-end">
                            {{-- Campo de búsqueda por nombre --}}
                            <div class="col-12 col-md-10">
                                <label for="search" class="form-label">Buscar por nombre</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por nombre...">
                            </div>

                            {{-- Filtro de sucursal oculto (sincronizado con el header via JS) --}}
                            <input type="hidden" name="branch_id" id="branch_id_filter">

                            {{-- Botones --}}
                            <div class="col-12 col-md-2 d-flex gap-2">
                                <button type="button" class="btn btn-secondary w-100" id="btn-clear-filters">
                                    <i class="bi bi-eraser-fill"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabla de Resultados --}}
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-white">ID</th>
                                    <th class="text-white">Nombre</th>
                                    <th class="text-white">Sucursal</th>
                                    <th class="text-center text-white">Stock</th>
                                    <th class="text-center text-white">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="assets-table-body">
                                @include('admin.assets.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    {{-- Paginación (Simplificada para el ejemplo JS, idealmente cargar via AJAX también) --}}
                    <div class="mt-3">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal para Modificar Stock --}}
@include('admin.assets.partials.stock-modal')

@endsection

@push('scripts')
<script src="{{ asset('js/admin/assets/index.js') }}"></script>
@endpush
