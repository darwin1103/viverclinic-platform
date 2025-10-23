@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>{{ __('Schedule Appointment') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Buy Package') }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">

                    <form id="formularioDepilacion" class="row g-4">

                        <!-- SECCIÓN 1: ESCOGER PAQUETES Y ZONeS ADICIONALES -->
                        <div class="col-12">
                            <h2 class="section-title">1. Paquete que deseo adquirir</h2>
                            @foreach ($packages as $paquete)
                                @include('components.buy-package.form.package-item', ['item' => $paquete, 'type' => 'package'])
                            @endforeach

                            <h2 class="section-title mt-4">Añadir una zone adicional</h2>
                            @foreach ($additionalZones as $zone)
                                @include('components.buy-package.form.package-item', ['item' => $zone, 'type' => 'additional'])
                            @endforeach
                        </div>

                        <!-- SECCIÓN 2: SELECCIONAR ZONeS -->
                        <div class="col-12">
                            <h2 class="section-title">2. Selecciona las zones deseadas a realizarte</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>Zonas Grandes</h3>
                                    <div id="zones-grandes-container">
                                        @foreach ($bigZones as $zone)
                                            @include('components.buy-package.form.zone-checkbox', ['zone' => $zone, 'type' => 'grande'])
                                        @endforeach
                                    </div>
                                    <h3>Zonas Pequeñas</h3>
                                    <div id="zones-pequenas-container">
                                        @foreach ($smallZones as $zone)
                                            @include('components.buy-package.form.zone-checkbox', ['zone' => $zone, 'type' => 'grande'])
                                        @endforeach
                                    </div>
                                    <div class="form-floating mt-3">
                                        <input type="text" class="form-control" id="another-big-zone" placeholder="Otra zone cual:">
                                        <label for="another-big-zone">Otra zona grande (opcional):</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h3>Mini Zonas</h3>
                                    <div id="mini-zones-container">
                                        @foreach ($miniZones as $zone)
                                            @include('components.buy-package.form.zone-checkbox', ['zone' => $zone, 'type' => 'mini'])
                                        @endforeach
                                    </div>
                                     <div class="form-floating mt-3">
                                        <input type="text" class="form-control" id="another-mini-zone" placeholder="Otra zone cual:">
                                        <label for="another-mini-zone">Otra mini zona (opcional):</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: BOTÓN INSTRUCTIVO -->
                        <div class="col-12 text-center">
                             <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#instructivoModal">
                                Ver Imagen de Zonas
                            </button>
                        </div>

                        <!-- SECCIÓN 4: RESUMEN DE COMPRA -->
                        <div class="col-12">
                            <h2 class="section-title">Resumen de tu Compra</h2>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody id="purchase-summary">
                                        <!-- El resumen se generará aquí con JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold fs-5">
                                            <td>TOTAL A PAGAR:</td>
                                            <td id="total" class="text-end">$0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#pagoModal">
                                    Pagar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Instructivo -->
<div class="modal fade" id="instructivoModal" tabindex="-1" aria-labelledby="instructivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructivoModalLabel">Zones de Depilación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Cambia esta URL por la de tu imagen -->
                <img src="/images/body-parts.jpg" class="img-fluid" alt="Instructivo de zones">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pago -->
<div class="modal fade" id="pagoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="pagoModalLabel">Formulario de Pago</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-pago">
                    <div class="mb-3">
                        <label for="name-tarjeta" class="form-label">name en la Tarjeta</label>
                        <input type="text" class="form-control" id="name-tarjeta" required>
                    </div>
                    <div class="mb-3">
                        <label for="numero-tarjeta" class="form-label">Número de Tarjeta</label>
                        <input type="text" class="form-control" id="numero-tarjeta" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha-exp" class="form-label">Fecha de Expiración</label>
                            <input type="text" class="form-control" id="fecha-exp" placeholder="MM/AA" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmar-pago">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const packages = @json($packages);
        const additionalZones = @json($additionalZones);
    </script>
    <script type="text/javascript" src="{{ asset('js/client/buy-package/form.js') }}"></script>
@endpush
