@extends('layouts.admin')
@section('content')
<div class="container">

    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card">
                <div class="card-body m-0 m-lg-3">
                    <h2 class="mb-3">Editar Sucursal</h2>
                    <form action="{{ route('branch.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">


                            <div class="col-12 col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nombre" value="{{ old('name', $branch->name) }}">
                                    <label for="name">Nombre</label>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Dirección" value="{{ old('address', $branch->address) }}">
                                    <label for="address">Dirección</label>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-floating my-3">
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Teléfono" value="{{ old('phone', $branch->phone) }}">
                                    <label for="phone">Teléfono</label>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-floating my-3">
                                    <input type="url" class="form-control @error('google_maps_url') is-invalid @enderror" id="google_maps_url" name="google_maps_url" placeholder="Teléfono" value="{{ old('google_maps_url', $branch->google_maps_url) }}">
                                    <label for="google_maps_url">Google maps URL</label>
                                    @error('google_maps_url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="logo" class="form-label">Imagen de Portada</label>
                                <input class="form-control @error('logo') is-invalid @enderror" type="file" id="logo" name="logo" accept="image/*">
                                @error('logo')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>

                            <div class="col-12 col-md-6 text-center">
                                <p class="mb-1">Imagen actual:</p>
                                <img id="imagePreview" src="{{ $branch->logo ? Storage::url($branch->logo) : '' }}" alt="Imagen actual" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>

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
@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImageInput = document.getElementById('logo');
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

@endpush
