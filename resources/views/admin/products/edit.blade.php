@extends('layouts.admin')

@section('content')
    <x-admin-card title="Editar Producto">
        <form method="POST" class="row g-2" action="{{ route('admin.products.update', $product->id) }}">
            @method('PUT')
            @include('admin.products.form')
        </form>
    </x-admin-card>
@endsection
