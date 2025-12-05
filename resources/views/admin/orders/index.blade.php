@extends('layouts.admin')

@section('content')

{{-- Sección de Filtros --}}
<div class="container mb-4">
    <div class="row">
        <div class="col-12">
            <h1>Ordenes</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" id="filter-form">
                <div class="row g-3 align-items-end">

                    {{-- Buscador --}}
                    <div class="col-12 col-md-4">
                        <label for="search" class="form-label">Buscar cliente</label>
                        <input type="text" class="form-control" id="search" name="search"
                               placeholder="Buscar por nombre o email del paciente..." value="{{ request('search') }}">
                    </div>

                    {{-- Fechas --}}
                    <div class="col-6 col-md-3">
                        <label for="date_from" class="form-label">Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="date_to" class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- Input oculto para Sucursal --}}
                    <input type="hidden" name="branch_id" id="branch_id_filter" value="{{ request('branch_id') }}">

                    {{-- Botón Limpiar --}}
                    <div class="col-12 col-md-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-eraser-fill"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Contenedor de la Tabla AJAX --}}
<div id="orders-table-container">
    @include('admin.orders.partials.table', ['orders' => $orders])
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/orders/index.js') }}"></script>
@endpush
