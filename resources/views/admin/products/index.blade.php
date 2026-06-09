@extends('layouts.admin')

@section('content')

{{-- Filtros (Estilo solicitado) --}}
<div class="container mb-4">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Productos</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-center text-md-end mb-3 mb-md-0" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.products.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo producto
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-end" id="filter-form">
                <div class="col-12 col-md-5">
                    <label class="form-label small mb-1">Buscar por nombre de producto</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                
                <input type="hidden" name="branch_id" id="branch_id_filter" value="{{ request('branch_id') }}">
                
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tabla de Resultados --}}
<div id="products-table-container">
    @include('admin.products.partials.table', ['products' => $products])
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/products/index.js') }}"></script>
@endpush
