@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <div class="row d-flex justify-content-center align-items-center mb-4">
                        <div class="col-12 text-center">
                            <h3 class="fw-bold">Editar Tratamiento: {{ $treatment->name }}</h3>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.treatment.update', $treatment) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Datos del Tratamiento --}}
                        <div class="row g-3">
                            <div class="col-12 col-md-3">
                                <div class="form-floating">
                                    <input id="name" type="text" placeholder="Nombre del Tratamiento" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $treatment->name) }}" required>
                                    <label for="name">Título del Tratamiento</label>
                                    @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-floating">
                                    <input id="sessions" type="number" placeholder="Cantidad de Sesiones" class="form-control @error('sessions') is-invalid @enderror" name="sessions" value="{{ old('sessions', $treatment->sessions) }}" required min="1">
                                    <label for="sessions">Cantidad de Sesiones</label>
                                    @error('sessions')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>
                             <div class="col-12 col-md-3">
                                <div class="form-floating">
                                    <input id="days_between_sessions" type="number" placeholder="Días entre Sesiones" class="form-control @error('days_between_sessions') is-invalid @enderror" name="days_between_sessions" value="{{ old('days_between_sessions', $treatment->days_between_sessions) }}" required min="0">
                                    <label for="days_between_sessions">Días entre Sesiones</label>
                                    @error('days_between_sessions')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea id="description" placeholder="Descripción" class="form-control @error('description') is-invalid @enderror" name="description" style="height: 120px" required>{{ old('description', $treatment->description) }}</textarea>
                                    <label for="description">Descripción</label>
                                    @error('description')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="main_image" class="form-label">Imagen de Portada</label>
                                <input class="form-control @error('main_image') is-invalid @enderror" type="file" id="main_image" name="main_image" accept="image/*">
                                @error('main_image')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>

                            <div class="col-12 col-md-3 text-center">
                                <p class="mb-1">Imagen actual:</p>
                                <img id="imagePreview" src="{{ $treatment->main_image ? Storage::url($treatment->main_image) : '' }}" alt="Imagen actual" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>

                             <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" value="1" @checked(old('active', $treatment->active))>
                                    <label class="form-check-label" for="active">Tratamiento Activo</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <h4 class="mb-3">Sucursales y Paquetes</h4>
                                {{-- Muestra de errores de validación para paquetes --}}
                                @error('branches.*.packages.*.name')<div class="alert alert-danger">El nombre del paquete es obligatorio.</div>@enderror
                                @error('branches.*.packages.*.price')<div class="alert alert-danger">El precio del paquete no es válido.</div>@enderror

                                <div class="accordion" id="accordionBranches">
                                    @foreach ($branches as $branch)
                                        @php
                                            // Agrupamos los paquetes existentes por sucursal
                                            $existingPackages = $treatment->packages->where('branch_id', $branch->id);
                                        @endphp
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-{{ $branch->id }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $branch->id }}" aria-expanded="false" aria-controls="collapse-{{ $branch->id }}">
                                                    {{ $branch->name }}
                                                    <span class="badge bg-primary rounded-pill ms-2">{{ $existingPackages->count() }} Paquetes</span>
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $branch->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $branch->id }}" data-bs-parent="#accordionBranches">
                                                <div class="accordion-body">
                                                    {{-- Contenedor para los formularios de paquetes de esta sucursal --}}
                                                    <div id="packages-container-{{ $branch->id }}">
                                                        @if(old('branches.'.$branch->id.'.packages'))
                                                             {{-- Repoblar con datos antiguos si falla la validación --}}
                                                             @foreach(old('branches.'.$branch->id.'.packages') as $key => $oldPackage)
                                                                @include('admin.treatment.partials.package_form', ['branch' => $branch, 'key' => $key, 'package' => (object)$oldPackage])
                                                             @endforeach
                                                        @else
                                                            {{-- Cargar paquetes existentes desde la base de datos --}}
                                                            @foreach ($existingPackages as $package)
                                                                @include('admin.treatment.partials.package_form', ['branch' => $branch, 'key' => $loop->index, 'package' => $package])
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <button type="button" class="btn btn-outline-primary mt-3 add-package-btn" data-branch-id="{{ $branch->id }}">
                                                        <i class="bi bi-plus-circle"></i> Agregar Paquete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <h4 class="my-3">Terminos y condiciones</h4>
                            <textarea name="terms_conditions" id="editor">
                                {!! $treatment->terms_conditions ?? old('terms_conditions') !!}
                            </textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-block text-center mt-4">
                            <a href="{{ route('admin.treatment.index') }}" class="btn btn-secondary w-auto">Cancelar</a>
                            <button type="submit" class="btn btn-primary w-auto">Guardar Cambios</button>
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



document.addEventListener('DOMContentLoaded', function() {
    // Usamos delegación de eventos para manejar clics en botones que aún no existen
    document.body.addEventListener('click', function(event) {
        // Botón para agregar un nuevo paquete
        if (event.target.classList.contains('add-package-btn')) {
            const branchId = event.target.dataset.branchId;
            const container = document.getElementById(`packages-container-${branchId}`);
            const newIndex = Date.now(); // Usamos timestamp para un índice único
            const packageFormHtml = getPackageFormTemplate(branchId, newIndex);

            // Insertamos el nuevo formulario en el contenedor
            container.insertAdjacentHTML('beforeend', packageFormHtml);
        }

        // Botón para eliminar un paquete
        if (event.target.closest('.remove-package-btn')) {
            event.preventDefault();
            // Buscamos el contenedor del paquete y lo eliminamos
            event.target.closest('.package-form-row').remove();
        }
    });

    function getPackageFormTemplate(branchId, index, packageData = {}) {
        const name = packageData.name || '';
        const price = packageData.price || '';
        const big_zones = packageData.big_zones || '';
        const mini_zones = packageData.mini_zones || '';

        return `
            <div class="row g-3 p-3 border rounded mb-3 package-form-row">
                <div class="col-12 col-md-3">
                    <label class="form-label">Nombre del paquete</label>
                    <input type="text" name="branches[${branchId}][packages][${index}][name]" class="form-control" placeholder="Ej: Paquete Premium" value="${name}" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Precio</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="branches[${branchId}][packages][${index}][price]" class="form-control" placeholder="0.00" step="0.01" min="0" value="${price}" required>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Zonas Grandes</label>
                    <input type="number" name="branches[${branchId}][packages][${index}][big_zones]" class="form-control" step="1" min="0" value="${big_zones}" required>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Mini Zonas</label>
                    <input type="number" name="branches[${branchId}][packages][${index}][mini_zones]" class="form-control" step="1" min="0" value="${mini_zones}" required>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger w-100 remove-package-btn">
                        <i class="bi bi-trash-fill"></i> Quitar
                    </button>
                </div>
            </div>
        `;
    }
});



</script>
@endsection

@push('styles')
    <link href="{{ asset('ckeditor/use/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/editor.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script type="text/javascript" src="{{ asset('ckeditor/build/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('ckeditor/use/script.js') }}"></script>
@endpush
