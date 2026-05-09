@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <h3 class="my-3">Datos del usuario</h3>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" value="{{ $client->name ?? '' }}" disabled>
                                <label for="name">{{ __('Name') }}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email Address') }}" value="{{ $client->email ?? '' }}" disabled>
                                <label for="email">{{ __('Email Address') }}</label>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="form-floating my-3">
                                <select id="branchId" class="form-control @error('branchId') is-invalid @enderror" name="branchId" value="{{ old('branchId') }}" required autocomplete="branchId" disabled>
                                    <option value="">Seleccionar</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @if ($branch->id == $client->patientProfile->branch->id) selected @endif >{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="branchId">Sucursal</label>
                                @error('branchId')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">

                    <div class="d-flex justify-content-between align-items-center my-3">
                        <h3 class="m-0">Historia clinica</h3>
                        @role('SUPER_ADMIN|OWNER')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editClinicalHistoryModal">
                            <i class="bi bi-pencil-square me-2"></i> Editar
                        </button>
                        @endrole
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="name" type="text" placeholder="{{__('Full Name')}}" class="form-control" name="name" value="{{ $client->name ?? '' }}" disabled>
                                <label for="name">{{__('Full Name')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="citizenship" type="text" placeholder="{{__('Citizenship')}}" class="form-control" name="citizenship" value="{{ $client->citizenship ?? '' }}" disabled>
                                <label for="citizenship">{{__('Citizenship')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="documentType" name="documentType" class="form-select " aria-label="documentType" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($documentTypes)&&count($documentTypes)>0)
                                        @foreach ($documentTypes as $item)
                                            <option value={{ $item->id }} @if($client->documentType != null && $client->documentType->id == $item->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="documentType">{{__('Document Type')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="documentNumber" type="text" placeholder="{{__('Document Number')}}" class="form-control" name="documentNumber" value="{{ $client->document_number ?? '' }}" disabled>
                                <label for="documentNumber">{{__('Document Number')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating mt-2">
                                <input id="birthday" type="date" placeholder="{{__('Birthday')}}" class="form-control" name="birthday" value="{{ $client->birthday ?? '' }}" disabled>
                                <label for="birthday">{{__('Birthday')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating mt-2">
                                <select id="gender" name="gender" class="form-select " aria-label="gender" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($genres)&&count($genres)>0)
                                        @foreach ($genres as $gender)
                                            <option value={{ $gender->id }} @if($client->gender != null && $gender->id == $client->gender->id) selected @endIf>{{ __($gender->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="gender">{{__('Gender')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating mt-2">
                                <input id="profession" type="text" placeholder="{{__('Occupation or Profession')}}" class="form-control" name="profession" value="{{ $client->profession ?? '' }}" disabled>
                                <label for="profession">{{__('Occupation or Profession')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="{{__('Cell Phone Number')}}" class="form-control" name="phone" value="{{ $client->phone ?? '' }}" disabled>
                                <label for="phone">{{__('Cell Phone Number')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="email" type="email" placeholder="{{__('Email Address')}}" class="form-control" name="email" value="{{ $client->email ?? '' }}" disabled>
                                <label for="email">{{__('Email Address')}}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mt-2">
                                <input id="address" type="text" placeholder="{{__('Address')}}" class="form-control" name="address" value="{{ $client->address ?? '' }}" disabled>
                                <label for="address">{{__('Address')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="pathologicalHistory" name="pathologicalHistory" class="form-select " aria-label="pathologicalHistory" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($pathologicalConditions)&&count($pathologicalConditions)>0)
                                        @foreach ($pathologicalConditions as $item)
                                            <option value={{ $item->id }} @if($client->pathologicalCondition != null && $item->id == $client->pathologicalCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="pathologicalHistory">{{__('Pathological History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="toxicologicalHistory" name="toxicologicalHistory" class="form-select " aria-label="toxicologicalHistory" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($toxicologicalConditions)&&count($toxicologicalConditions)>0)
                                        @foreach ($toxicologicalConditions as $item)
                                            <option value={{ $item->id }} @if($client->toxicologicalCondition != null && $item->id == $client->toxicologicalCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="toxicologicalHistory">{{__('Toxicological History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="gynecoObstetricHistory" name="gynecoObstetricHistory" class="form-select " aria-label="gynecoObstetricHistory" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($gynecoObstetricConditions)&&count($gynecoObstetricConditions)>0)
                                        @foreach ($gynecoObstetricConditions as $item)
                                            <option value={{ $item->id }} @if($client->gynecoObstetricCondition != null && $item->id == $client->gynecoObstetricCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="gynecoObstetricHistory">{{__('Gyneco Obstetric History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="medications" name="medications" class="form-select " aria-label="medications" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($medicationConditions)&&count($medicationConditions)>0)
                                        @foreach ($medicationConditions as $item)
                                            <option value={{ $item->id }} @if($client->medicationCondition != null && $item->id == $client->medicationCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="medications">{{__('Are you taking any of these medications?')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="dietaryHistory" name="dietaryHistory" class="form-select " aria-label="dietaryHistory" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($dietaryConditions)&&count($dietaryConditions)>0)
                                        @foreach ($dietaryConditions as $item)
                                            <option value={{ $item->id }} @if($client->dietaryCondition != null && $item->id == $client->dietaryCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="dietaryHistory">{{__('Dietary History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="treatment" name="treatment" class="form-select  load-informed-consent-contract" aria-label="treatment" disabled>
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($treatments)&&count($treatments)>0)
                                        @foreach ($treatments as $item)
                                            <option data-contract-text="{{ $item->terms_conditions }}" value={{ $item->id }} @if($client->treatment != null && $item->id == $client->treatment->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="treatment">{{__('What treatment are you going to have?')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="surgery" type="text" placeholder="{{__('Do you have any surgery? Which one?')}}" class="form-control" name="surgery" value="{{ $client->surgery ?? '' }}" disabled>
                                <label for="surgery">{{__('Do you have any surgery? Which one?')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="recommendation" type="text" placeholder="{{__('Did anyone recommend Viverclinic to you?')}}" class="form-control" name="recommendation" value="{{ $client->recommendation ?? '' }}" disabled>
                                <label for="recommendation">{{__('Did anyone recommend Viverclinic to you?')}}</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Clinical History -->
<div class="modal fade" id="editClinicalHistoryModal" tabindex="-1" aria-labelledby="editClinicalHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClinicalHistoryModalLabel">Editar Historia Clínica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.clients.clinical-history.update', $client) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <!-- Personal Info -->
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_name" type="text" class="form-control" name="name" value="{{ $client->name ?? '' }}">
                                <label for="edit_name">{{__('Full Name')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_citizenship" type="text" class="form-control" name="citizenship" value="{{ $client->citizenship ?? '' }}">
                                <label for="edit_citizenship">{{__('Citizenship')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_documentType" name="documentType" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($documentTypes)&&count($documentTypes)>0)
                                        @foreach ($documentTypes as $item)
                                            <option value="{{ $item->id }}" @if($client->documentType != null && $client->documentType->id == $item->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_documentType">{{__('Document Type')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_documentNumber" type="text" class="form-control" name="documentNumber" value="{{ $client->document_number ?? '' }}">
                                <label for="edit_documentNumber">{{__('Document Number')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating mt-2">
                                <input id="edit_birthday" type="date" class="form-control" name="birthday" value="{{ $client->birthday ?? '' }}">
                                <label for="edit_birthday">{{__('Birthday')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating mt-2">
                                <select id="edit_gender" name="gender" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($genres)&&count($genres)>0)
                                        @foreach ($genres as $gender)
                                            <option value="{{ $gender->id }}" @if($client->gender != null && $gender->id == $client->gender->id) selected @endif>{{ __($gender->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_gender">{{__('Gender')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating mt-2">
                                <input id="edit_profession" type="text" class="form-control" name="profession" value="{{ $client->profession ?? '' }}">
                                <label for="edit_profession">{{__('Occupation or Profession')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_phone" type="tel" inputmode="tel" autocomplete="tel" class="form-control" name="phone" value="{{ $client->phone ?? '' }}">
                                <label for="edit_phone">{{__('Cell Phone Number')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_email" type="email" class="form-control" name="email" value="{{ $client->email ?? '' }}">
                                <label for="edit_email">{{__('Email Address')}}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mt-2">
                                <input id="edit_address" type="text" class="form-control" name="address" value="{{ $client->address ?? '' }}">
                                <label for="edit_address">{{__('Address')}}</label>
                            </div>
                        </div>
                        
                        <!-- Medical History -->
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_pathologicalHistory" name="pathologicalHistory" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($pathologicalConditions)&&count($pathologicalConditions)>0)
                                        @foreach ($pathologicalConditions as $item)
                                            <option value="{{ $item->id }}" @if($client->pathologicalCondition != null && $item->id == $client->pathologicalCondition->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_pathologicalHistory">{{__('Pathological History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_toxicologicalHistory" name="toxicologicalHistory" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($toxicologicalConditions)&&count($toxicologicalConditions)>0)
                                        @foreach ($toxicologicalConditions as $item)
                                            <option value="{{ $item->id }}" @if($client->toxicologicalCondition != null && $item->id == $client->toxicologicalCondition->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_toxicologicalHistory">{{__('Toxicological History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_gynecoObstetricHistory" name="gynecoObstetricHistory" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($gynecoObstetricConditions)&&count($gynecoObstetricConditions)>0)
                                        @foreach ($gynecoObstetricConditions as $item)
                                            <option value="{{ $item->id }}" @if($client->gynecoObstetricCondition != null && $item->id == $client->gynecoObstetricCondition->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_gynecoObstetricHistory">{{__('Gyneco Obstetric History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_medications" name="medications" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($medicationConditions)&&count($medicationConditions)>0)
                                        @foreach ($medicationConditions as $item)
                                            <option value="{{ $item->id }}" @if($client->medicationCondition != null && $item->id == $client->medicationCondition->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_medications">{{__('Are you taking any of these medications?')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_dietaryHistory" name="dietaryHistory" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($dietaryConditions)&&count($dietaryConditions)>0)
                                        @foreach ($dietaryConditions as $item)
                                            <option value="{{ $item->id }}" @if($client->dietaryCondition != null && $item->id == $client->dietaryCondition->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_dietaryHistory">{{__('Dietary History')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <select id="edit_treatment" name="treatment" class="form-select">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($treatments)&&count($treatments)>0)
                                        @foreach ($treatments as $item)
                                            <option value="{{ $item->id }}" @if($client->treatment != null && $item->id == $client->treatment->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="edit_treatment">{{__('What treatment are you going to have?')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_surgery" type="text" class="form-control" name="surgery" value="{{ $client->surgery ?? '' }}">
                                <label for="edit_surgery">{{__('Do you have any surgery? Which one?')}}</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating mt-2">
                                <input id="edit_recommendation" type="text" class="form-control" name="recommendation" value="{{ $client->recommendation ?? '' }}">
                                <label for="edit_recommendation">{{__('Did anyone recommend Viverclinic to you?')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
