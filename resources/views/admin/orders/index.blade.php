@extends('layouts.admin')

@section('content')

{{-- Sección de Filtros --}}
<div class="container mb-4">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Ordenes</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.manual-sales.index') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Añadir venta
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-end" id="filter-form">
                <div class="col-12 col-md-5">
                    <label class="form-label small mb-1">Buscar cliente</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Buscar por nombre o email..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                
                <input type="hidden" name="branch_id" id="branch_id_filter" value="{{ request('branch_id') }}">
                
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
