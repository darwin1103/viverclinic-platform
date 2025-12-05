@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Mis Compras</h2>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-body">
            <form id="filter-form" action="{{ route('client.orders.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="date_from" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label for="date_to" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('client.orders.index') }}'">
                        <i class="bi bi-eraser-fill"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Contenedor de Tabla Ajax --}}
    <div id="orders-table-container">
        @include('client.orders.partials.table', ['orders' => $orders])
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/client/orders/index.js') }}"></script>
@endpush
