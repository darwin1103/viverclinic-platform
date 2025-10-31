@extends('layouts.admin')
@section('content')
<div class="container">
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
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="requestInformedConsent" name="requestInformedConsent" @if($client->informed_consent) checked @endif disabled>
                        <label class="form-check-label" for="requestInformedConsent">
                            {{ __('Request Informed Consent') }}
                        </label>
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

                    <h3 class="my-3">Historia clinica</h3>

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
                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input  show-terms-conditions-modal" disabled type="checkbox" id="termsConditions" name="termsConditions" @if($client->terms_conditions) checked @endif>
                                <label class="form-check-label" for="termsConditions">
                                    {{ __('I have clearly read the consent I accept terms and conditions') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notPregnant" name="notPregnant" @if($client->not_pregnant) checked @endif disabled>
                                <label class="form-check-label" for="notPregnant">
                                    {{ __('Im not pregnant') }}
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
