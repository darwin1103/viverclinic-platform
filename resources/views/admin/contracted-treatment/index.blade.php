@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Tratamientos Contratados</h1>
        </div>

    </div>

    <div class="row">
        <div class="col-12">

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
