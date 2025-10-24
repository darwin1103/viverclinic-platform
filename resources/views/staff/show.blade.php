@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Datos del usuario</h1>

    <div class="tabset mx-2">

        <!-- Tab 1 -->
        <input type="radio" name="tabset" id="tab1" aria-controls="generales" checked>
        <label for="tab1">Datos generales</label>

        <!-- Tab 2 -->
        <input type="radio" name="tabset" id="tab2" aria-controls="appointments">
        <label for="tab2">Citas programadas</label>

        <!-- Tab 2 -->
        <input type="radio" name="tabset" id="tab3" aria-controls="old-appointments">
        <label for="tab3">Citas pasadas</label>

        <!-- Tab 3 -->
        <input type="radio" name="tabset" id="tab4" aria-controls="clients">
        <label for="tab4">Clientes</label>

        <div class="tab-panels">

            <section id="generales" class="tab-panel">

                <x-admin.staff.show.basic-data
                    :staff="$staff"
                    :schedules="$schedules"
                    :branches="$branches"
                    :daysOfWeek="$daysOfWeek"
                />

            </section>

            <section id="appointments" class="tab-panel">

            </section>

            <section id="old-appointments" class="tab-panel">

            </section>

            <section id="clients" class="tab-panel">

            </section>

        </div>

    </div>

</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/general/tabs.css') }}">
@endpush
