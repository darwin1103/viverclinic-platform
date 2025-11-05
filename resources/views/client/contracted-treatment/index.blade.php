@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-10 mx-auto">
            {{-- Título de la página --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Tratamientos Contratados</h1>
                {{-- Aquí puedes agregar un botón para crear un nuevo registro si es necesario --}}
            </div>

            {{-- Componente de la Tabla --}}
            <x-client.contracted-treatment.treatments-table
                :contractedTreatments="$contractedTreatments"
            />

        </div>
    </div>
</div>
@endsection
