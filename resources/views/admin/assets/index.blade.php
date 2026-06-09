@extends('layouts.admin')

@section('content')
<div class="container-fluid">
        <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Activos</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.assets.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo activo
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('admin.assets.index') }}" class="row g-2 align-items-end" id="filter-form">
                        <div class="col-12 col-md-5">
                            <label class="form-label small mb-1">Buscar por nombre</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                        </div>
                        
                        <input type="hidden" name="branch_id" id="branch_id_filter" value="{{ request('branch_id') }}">
                        
                        <div class="col-12 col-md-1 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                            <a href="{{ route('admin.assets.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
