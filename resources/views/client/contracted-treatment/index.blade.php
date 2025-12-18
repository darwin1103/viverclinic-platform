@extends('layouts.app')

@section('content')
<div class="container-fluid p-0 py-4">
    <div class="row">
        <div class="col-12">
            {{-- Título de la página --}}
            <div class="d-flex justify-content-between align-items-center ">
                <h1 class="">Tratamientos Contratados</h1>
            </div>

            <x-client.contracted-treatment.treatments-table
                :contractedTreatments="$contractedTreatments"
            />

        </div>
    </div>
</div>
@endsection
