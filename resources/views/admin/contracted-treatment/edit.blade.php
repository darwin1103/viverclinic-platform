@extends('layouts.admin')

@section('title', 'Editar Tratamiento Contratado')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Editar Tratamiento Contratado</h1>
            <p class="mb-4">Modifica los detalles de sesiones, frecuencia, estado y zonas del tratamiento de <strong>{{ $contractedTreatment->user->name ?? 'N/A' }}</strong>.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.contracted-treatment.update', $contractedTreatment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <h4 class="mb-3 text-primary border-bottom pb-2">1. Datos Generales</h4>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="sessions" class="form-label fw-bold">Número de Sesiones</label>
                        <input type="number" name="sessions" id="sessions" class="form-control" min="1" value="{{ old('sessions', $contractedTreatment->sessions) }}" required>
                        <small class="text-muted">Total de sesiones contratadas en este plan.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="days_between_sessions" class="form-label fw-bold">Frecuencia (Días entre sesiones)</label>
                        <input type="number" name="days_between_sessions" id="days_between_sessions" class="form-control" min="0" value="{{ old('days_between_sessions', $contractedTreatment->days_between_sessions) }}" required>
                        <small class="text-muted">Días de separación recomendados entre cada cita.</small>
                    </div>
                </div>

                <h4 class="mb-3 text-primary border-bottom pb-2">2. Zonas del Tratamiento</h4>
                <div class="row mb-4">
                    @php
                        $selectedBig = $contractedTreatment->selected_zones['big'] ?? [];
                        $selectedMini = $contractedTreatment->selected_zones['mini'] ?? [];
                    @endphp
                    <div class="col-md-6">
                        <h5 class="text-secondary">Zonas Grandes</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($bigZones as $zone)
                                <div class="form-check form-check-inline p-2 m-0">
                                    <input class="form-check-input ms-1" type="checkbox" name="selected_zones[big][]" id="big_{{ Str::slug($zone) }}" value="{{ $zone }}" {{ in_array($zone, $selectedBig) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 pe-2" for="big_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                </div>
                            @endforeach
                        </div>
                        
                        <h5 class="text-secondary mt-3">Zonas Pequeñas</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($smallZones as $zone)
                                <div class="form-check form-check-inline p-2 m-0">
                                    <input class="form-check-input ms-1" type="checkbox" name="selected_zones[big][]" id="small_{{ Str::slug($zone) }}" value="{{ $zone }}" {{ in_array($zone, $selectedBig) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 pe-2" for="small_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Mostrar las zonas custom big que no están en el catálogo estándar --}}
                        @php
                            $standardBig = array_merge($bigZones, $smallZones);
                            $customBig = array_diff($selectedBig, $standardBig);
                        @endphp
                        @if(!empty($customBig))
                            <h6 class="text-warning mt-2">Zonas Personalizadas Actuales:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach($customBig as $czone)
                                    <div class="form-check form-check-inline p-2 m-0">
                                        <input class="form-check-input ms-1" type="checkbox" name="selected_zones[big][]" id="custom_big_{{ Str::slug($czone) }}" value="{{ $czone }}" checked>
                                        <label class="form-check-label ms-2 pe-2" for="custom_big_{{ Str::slug($czone) }}">{{ $czone }} <small class="text-muted">(Personalizada)</small></label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="another-big-zone" name="another_big_zone" placeholder="Otra zona grande">
                            <label for="another-big-zone">Agregar otra zona grande o pequeña</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-secondary">Mini Zonas</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($miniZones as $zone)
                                <div class="form-check form-check-inline p-2 m-0">
                                    <input class="form-check-input ms-1" type="checkbox" name="selected_zones[mini][]" id="mini_{{ Str::slug($zone) }}" value="{{ $zone }}" {{ in_array($zone, $selectedMini) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 pe-2" for="mini_{{ Str::slug($zone) }}">{{ $zone }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Mostrar las zonas custom mini que no están en el catálogo estándar --}}
                        @php
                            $customMini = array_diff($selectedMini, $miniZones);
                        @endphp
                        @if(!empty($customMini))
                            <h6 class="text-warning mt-2">Zonas Mini Personalizadas Actuales:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach($customMini as $czone)
                                    <div class="form-check form-check-inline p-2 m-0">
                                        <input class="form-check-input ms-1" type="checkbox" name="selected_zones[mini][]" id="custom_mini_{{ Str::slug($czone) }}" value="{{ $czone }}" checked>
                                        <label class="form-check-label ms-2 pe-2" for="custom_mini_{{ Str::slug($czone) }}">{{ $czone }} <small class="text-muted">(Personalizada)</small></label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-floating mt-3">
                            <input type="text" class="form-control" id="another-mini-zone" name="another_mini_zone" placeholder="Otra mini zona">
                            <label for="another-mini-zone">Agregar otra mini zona</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.contracted-treatment.show', $contractedTreatment->id) }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
