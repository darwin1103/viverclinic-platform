@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Datos del propietario</h1>

    <x-admin.owner.show.basic-data
        :owner="$owner"
    />

</div>
@endsection
