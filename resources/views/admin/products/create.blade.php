@extends('layouts.admin')

@section('content')
    <x-admin-card title="Crear Nuevo Producto">
        <form method="POST" class="row g-2" action="{{ route('admin.products.store') }}">
            @include('admin.products.form')
        </form>
    </x-admin-card>
@endsection
