@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Editar</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card" style="width: 30rem;">
                <div class="card-body m-0 m-lg-3">
                    <h2 class="mb-3">Editar Sucursal</h2>
                    <form action="{{ route('branch.update', $branch->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nombre" value="{{ old('name', $branch->name) }}">
                            <label for="name">Nombre</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating my-3">
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Dirección" value="{{ old('address', $branch->address) }}">
                            <label for="address">Dirección</label>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-floating my-3">
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" placeholder="Teléfono" value="{{ old('telephone', $branch->phone) }}">
                            <label for="telephone">Teléfono</label>
                            @error('telephone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mt-3 text-end">
                            <a href="{{ route('branch.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
