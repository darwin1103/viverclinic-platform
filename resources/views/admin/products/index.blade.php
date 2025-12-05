@extends('layouts.admin')

@section('content')

{{-- Filtros (Estilo solicitado) --}}
<div class="container mb-4">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h1>Productos</h1>
        </div>
        <div class="col-12 col-md-6 col-lg-8 text-end" style="align-content: center;">
            <a class="btn btn-primary" href="{{ route('admin.products.create') }}" role="button">
                <i class="bi bi-plus-circle-fill"></i>&nbsp;Crear nuevo producto
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" id="filter-form">
                <div class="row g-3 align-items-end">
                    {{-- Campo de búsqueda por nombre --}}
                    <div class="col-12 col-md-10">
                        <label for="search" class="form-label">Buscar por nombre de producto</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                    </div>

                    {{-- Campo oculto para el filtro de sucursal (controlado por JS desde el header) --}}
                    <input type="hidden" name="branch_id" id="branch_id_filter" value="{{ request('branch_id') }}">

                    {{-- Botones --}}
                    <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-eraser-fill"></i> Limpiar
                        </a>
                    </div>
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
