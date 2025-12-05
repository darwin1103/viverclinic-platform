@extends('layouts.admin')

@section('content')
<div class="container-fluid pb-5">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- Columna Izquierda: Grid de Productos --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Productos Disponibles</h5>
                        {{-- Buscador de Productos --}}
                        <div class="w-50">
                            <input type="text" id="product-search" class="form-control" placeholder="Buscar producto...">
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="max-height: 80vh;">
                    <div id="products-container" class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col-12 text-center py-5 text-muted">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Cargando productos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Configuración de Venta y Carrito --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Nueva Venta</h5>
                </div>
                <div class="card-body d-flex flex-column">

                    <form id="sale-form" action="{{ route('admin.manual-sales.store') }}" method="POST">
                        @csrf

                        {{-- Input Oculto para Branch (se sincroniza con JS) --}}
                        <input type="hidden" name="branch_id" id="form_branch_id">

                        {{-- Input Oculto para los items del carrito (JSON) --}}
                        <input type="hidden" name="items" id="cart_items_json">

                        {{-- Selector de Paciente con Buscador --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Paciente</label>
                            <input type="text" id="patient-search" class="form-control mb-1" placeholder="Buscar por nombre o email..." autocomplete="off">
                            <select name="user_id" id="patient-select" class="form-select" size="3" required>
                                <option value="" disabled selected>Busca y selecciona un paciente</option>
                            </select>
                            <div class="form-text text-muted" id="patient-helper">
                                * Se muestran pacientes de la sucursal seleccionada.
                            </div>
                        </div>

                        {{-- Método de Pago --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Método de Pago</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Punto de Venta">Punto de Venta</option>
                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                            </select>
                        </div>

                        <hr>

                        {{-- Tabla Resumen Carrito --}}
                        <h6 class="fw-bold mb-3">Resumen de Compra</h6>
                        <div class="table-responsive mb-3 flex-grow-1" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-white">Producto</th>
                                        <th style="width: 70px;" class="text-white">Cantidad</th>
                                        <th class="text-end text-white">Total</th>
                                        <th style="width: 30px;" class="text-white"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-table-body">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted small py-3">
                                            Carrito vacío
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Total y Botón --}}
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded">
                                <span class="h5 mb-0">Total a Pagar:</span>
                                <span class="h4 mb-0 text-primary fw-bold" id="cart-total">$ 0</span>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg" id="btn-submit" disabled>
                                    <i class="bi bi-check-circle-fill"></i> Registrar Venta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- URLs para JS --}}
<script>
    const urls = {
        products: "{{ route('admin.manual-sales.products') }}",
        patients: "{{ route('admin.manual-sales.patients') }}"
    };
</script>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/manual-sales/index.js') }}"></script>
@endpush
