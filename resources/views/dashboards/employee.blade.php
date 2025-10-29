@extends('layouts.employee')
@section('content')
    <div class="container">
        <div class="row justify-content-center my-2">
            <h1 class="fw-bold m-0" style="color: #fffffd;">¡{{ __('Hello') }}, {{ Auth::user()->name }}!</h1>
        </div>

    </div>
@endsection
