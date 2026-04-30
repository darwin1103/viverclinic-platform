@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Módulo Contable (Flujo de Caja y Cierre Diario)</h2>
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-header">Ingresos Diarios</div>
                <div class="card-body">
                    <h4 class="card-title">${{ number_format($ingresosDiarios, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3 shadow-sm">
                <div class="card-header">Egresos Diarios</div>
                <div class="card-body">
                    <h4 class="card-title">${{ number_format($egresosDiarios, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow-sm">
                <div class="card-header">Ventas Totales</div>
                <div class="card-body">
                    <h4 class="card-title">${{ number_format($ventasTotales, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow-sm text-dark">
                <div class="card-header">Comisión Admin (3.33%)</div>
                <div class="card-body">
                    <h4 class="card-title">${{ number_format($comisionAdmin, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
