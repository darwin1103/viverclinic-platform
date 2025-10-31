@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <div class="row d-flex justify-content-center align-items-center mb-4">
                        <div class="col-12 text-center">
                            <h3 class="fw-bold">Crear Nuevo Tratamiento</h3>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.treatment.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Datos del Tratamiento --}}
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="form-floating">
                                    <input id="name" type="text" placeholder="Título del Tratamiento" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                    <label for="name">Título del Tratamiento</label>
                                    @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-floating">
                                    <input id="sessions" type="number" placeholder="Cantidad de Sesiones" class="form-control @error('sessions') is-invalid @enderror" name="sessions" value="{{ old('sessions') }}" required min="1">
                                    <label for="sessions">Cantidad de Sesiones</label>
                                    @error('sessions')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>
                             <div class="col-12 col-md-3">
                                <div class="form-floating">
                                    <input id="days_between_sessions" type="number" placeholder="Días entre Sesiones" class="form-control @error('days_between_sessions') is-invalid @enderror" name="days_between_sessions" value="{{ old('days_between_sessions') }}" required min="0">
                                    <label for="days_between_sessions">Días entre Sesiones</label>
                                    @error('days_between_sessions')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea id="description" placeholder="Descripción" class="form-control @error('description') is-invalid @enderror" name="description" style="height: 120px" required>{{ old('description') }}</textarea>
                                    <label for="description">Descripción</label>
                                    @error('description')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="main_image" class="form-label">Imagen de Portada</label>
                                <input class="form-control @error('main_image') is-invalid @enderror" type="file" id="main_image" name="main_image" accept="image/*">
                                @error('main_image')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>

                            <div class="col-12 col-md-6 text-center">
                                <p class="mb-1">Vista previa:</p>
                                <img id="imagePreview" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>

                             <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" value="1" checked>
                                    <label class="form-check-label" for="active">Tratamiento Activo</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Gestión de Sucursales y Precios --}}
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mb-3">Asignar a Sucursales y Definir Precios</h4>
                                @error('branches.*.price')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Asignar</th>
                                                <th>Sucursal</th>
                                                <th>Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($branches as $branch)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="branches[{{ $branch->id }}][attach]" id="branch_{{ $branch->id }}" @checked(old('branches.'.$branch->id.'.attach'))>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="branch_{{ $branch->id }}">
                                                            {{ $branch->name }}
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" name="branches[{{ $branch->id }}][price]" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ old('branches.'.$branch->id.'.price') }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-block text-center mt-4">
                            <a href="{{ route('admin.treatment.index') }}" class="btn btn-secondary w-auto">Cancelar</a>
                            <button type="submit" class="btn btn-primary w-auto">Crear Tratamiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImageInput = document.getElementById('main_image');
    const imagePreview = document.getElementById('imagePreview');

    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }
});
</script>
@endsection
