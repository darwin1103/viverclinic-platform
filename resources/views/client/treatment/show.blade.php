@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>Comprar paquete</h1>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" class="row g-4" action="{{ route('client.treatment.store') }}">
                        @csrf

                        <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">

                        <!-- SECCIÓN 1: ESCOGER PAQUETES Y ZONAS ADICIONALES -->
                        <div class="col-12">
                            <h2 class="section-title">1. Paquete que deseo adquirir</h2>
                            @foreach ($packages as $paquete)
                                @include('components.client.index.form.package-item', ['item' => $paquete, 'type' => 'package'])
                            @endforeach

                            <h2 class="section-title mt-4">Añadir una zona adicional</h2>
                            @foreach ($additionalZones as $zone)
                                @include('components.client.index.form.package-item', ['item' => $zone, 'type' => 'additional'])
                            @endforeach
                        </div>

                        <!-- SECCIÓN 2: SELECCIONAR ZONeS -->
                        <div class="col-12">
                            <h2 class="section-title">2. Selecciona las zonas deseadas a realizarte</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>Zonas Grandes</h3>
                                    <div id="zones-grandes-container">
                                        @foreach ($bigZones as $zone)
                                            @include('components.client.index.form.zone-checkbox', ['zone' => $zone, 'type' => 'big'])
                                        @endforeach
                                    </div>
                                    <h3>Zonas Pequeñas</h3>
                                    <div id="zones-pequenas-container">
                                        @foreach ($smallZones as $zone)
                                            @include('components.client.index.form.zone-checkbox', ['zone' => $zone, 'type' => 'big'])
                                        @endforeach
                                    </div>
                                    <div class="form-floating mt-3">
                                        <input type="text" class="form-control" id="another-big-zone" name="another_big_zone" placeholder="Otra zona cual:">
                                        <label for="another-big-zone">Otra zona grande (opcional):</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h3>Mini Zonas</h3>
                                    <div id="mini-zones-container">
                                        @foreach ($miniZones as $zone)
                                            @include('components.client.index.form.zone-checkbox', ['zone' => $zone, 'type' => 'mini'])
                                        @endforeach
                                    </div>
                                     <div class="form-floating mt-3">
                                        <input type="text" class="form-control" id="another-mini-zone" name="another_mini_zone" placeholder="Otra mini zona cual:">
                                        <label for="another-mini-zone">Otra mini zona (opcional):</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: BOTÓN INSTRUCTIVO -->
                        <div class="col-12 text-center">
                             <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#instructivoModal">
                                Ver Imagen de zonas
                            </button>
                        </div>

                        <!-- SECCIÓN 4: RESUMEN DE COMPRA -->
                        <div class="col-12" id="purchase-sumary-container">
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

                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input @error('termsConditions') is-invalid @enderror show-terms-conditions-modal" type="checkbox" value="1" id="termsConditions" name="termsConditions" required>
                                    <label class="form-check-label" for="termsConditions">
                                        {{ __('I have clearly read the consent I accept terms and conditions') }}
                                    </label>
                                    @error('termsConditions')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="notPregnant" name="notPregnant" value="1" >
                                    <label class="form-check-label" for="notPregnant">
                                        {{ __('Im not pregnant') }}
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="payment-button-container" disabled>
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
<x-client.index.form.body-modal />

<div class="modal fade" id="termsConditionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="termsConditionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="termsConditionsModalLabel">{{ __('Terms and Conditions') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $treatment->terms_conditions !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="acceptTermsConditions">{{ __('I accept') }}</button>
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

    <script type="text/javascript" src="{{ asset('js/client/treatment/show/form.js') }}"></script>
@endpush
