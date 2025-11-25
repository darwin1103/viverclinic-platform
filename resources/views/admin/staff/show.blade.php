@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Datos del trabajador</h1>

    <div class="tabset mx-2">

        <!-- Tab 1 -->
        <input type="radio" name="tabset" id="tab1" aria-controls="generales" @if(!$isOnAppointmentTable) checked @endif >
        <label for="tab1">Datos generales</label>

        <!-- Tab 3 -->
        <input type="radio" name="tabset" id="tab2" aria-controls="old-appointments" @if($isOnAppointmentTable) checked @endif >
        <label for="tab2">Citas pasadas</label>

        <div class="tab-panels">

            <section id="generales" class="tab-panel">

                <x-admin.staff.show.basic-data
                    :staff="$staff"
                    :schedules="$schedules"
                    :branches="$branches"
                    :daysOfWeek="$daysOfWeek"
                />

            </section>

            <section id="old-appointments" class="tab-panel">
                <x-admin.staff.show.appointments :staff="$staff" :appointments="$appointments" :treatments="$treatments" title="Citas pasadas"/>
            </section>

        </div>

    </div>

</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/general/tabs.css') }}">
@endpush
