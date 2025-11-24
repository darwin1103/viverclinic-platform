@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Título de la página --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Tratamientos Contratados</h1>
                {{-- Aquí puedes agregar un botón para crear un nuevo registro si es necesario --}}
            </div>

            {{-- Componente de Filtros --}}
            <x-admin.contracted-treatment.filter-card
                :treatments="$treatments"
            />

            {{-- Componente de la Tabla --}}
            <x-admin.contracted-treatment.treatments-table
                :contractedTreatments="$contractedTreatments"
            />

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/contracted-treatment/index.js') }}"></script>
@endpush
