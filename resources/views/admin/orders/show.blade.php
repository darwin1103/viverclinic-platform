@extends('layouts.admin')

@section('content')
<div class="container py-4">
    {{-- Header con Botón Volver --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Detalle de Orden #{{ $order->id }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="row g-4">
        {{-- Columna Izquierda: Detalles de la Orden y Items --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Productos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio unidad</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-4">{{ $item->product_name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-end pe-4">$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-3">TOTAL:</td>
                                    <td class="text-end pe-4 fw-bold pt-3 fs-5">
                                        $ {{ number_format($order->total, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Info Cliente y Gestión de Estado --}}
        <div class="col-12 col-lg-4">
            {{-- Info Cliente --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Información del Cliente</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nombre:</strong> {{ $order->user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p class="mb-1"><strong>Documento:</strong> {{ $order->document_type }} {{ $order->document_number }}</p>
                    <p class="mb-0"><strong>Sucursal:</strong> {{ $order->branch->name }}</p>
                </div>
            </div>

            {{-- Info Pago --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Detalles del Pago</h6>
                </div>
                <div class="card-body small">
                    <p class="mb-1"><strong>Referencia:</strong> {{ $order->payment_reference ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Método:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y h:i a') }}</p>
                </div>
            </div>

            {{-- Gestión de Estado (Formulario) --}}
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-gear-fill"></i> Gestionar Estado</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado Actual</label>
                            <select name="status" id="status" class="form-select form-select-lg">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Actualizar Estado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
