@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <h4 class="card-title mb-4">Crear Nuevo Activo</h4>

                    <form method="POST" action="{{ route('admin.assets.store') }}" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nombre del activo" value="{{ old('name') }}" required>
                                <label for="name">Nombre del Activo</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="branch_id">Sucursal</label>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" placeholder="0" value="{{ old('stock', 0) }}" min="0" required>
                                <label for="stock">Stock Inicial</label>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Activo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
