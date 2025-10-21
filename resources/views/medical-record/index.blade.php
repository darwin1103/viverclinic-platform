@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-md-7 col-lg-8">
            <h1>{{ __('Medical Record') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Medical Record') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body">
                    <div class="card-body m-0 m-lg-3">
                        <div class="row g-2">
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <input id="name" type="text" placeholder="{{__('Full Name')}}" class="form-control" value="{{ Auth::user()->name ?? '' }}" disabled>
                                    <label for="name">{{__('Full Name')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-1">
                                <div class="form-floating">
                                    <input id="age" type="text" placeholder="{{__('Age')}}" class="form-control" value="{{ Auth::user()->birthday ? \Carbon\Carbon::parse(Auth::user()->birthday)->age : '' }}" disabled>
                                    <label for="age">{{__('Age')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="form-floating">
                                    <input id="documentNumber" type="text" placeholder="{{__('Document Number')}}" class="form-control" value="{{ Auth::user()->document_number ?? '' }}" disabled>
                                    <label for="documentNumber">{{__('Document Number')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-floating">
                                    <input id="profession" type="text" placeholder="{{__('Occupation or Profession')}}" class="form-control" value="{{ Auth::user()->profession ?? '' }}" disabled>
                                    <label for="profession">{{__('Occupation or Profession')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="form-floating">
                                    <input id="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="{{__('Cell Phone Number')}}" class="form-control" value="{{ Auth::user()->phone ?? '' }}" disabled>
                                    <label for="phone">{{__('Cell Phone Number')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="form-floating">
                                    <input id="email" type="email" placeholder="{{__('Email Address')}}" class="form-control" value="{{ Auth::user()->email ?? '' }}" disabled>
                                    <label for="email">{{__('Email Address')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <select id="pathologicalHistory" name="pathologicalHistory" class="form-select" aria-label="pathologicalHistory" disabled>
                                        <option value="">{{__('Not defined')}}</option>
                                        @if (isset($pathologicalConditions)&&count($pathologicalConditions)>0)
                                            @foreach ($pathologicalConditions as $item)
                                                <option value={{ $item->id }} @if(Auth::user()->pathologicalCondition != null && $item->id == Auth::user()->pathologicalCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="pathologicalHistory">{{__('Pathological History')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <select id="toxicologicalHistory" name="toxicologicalHistory" class="form-select" aria-label="toxicologicalHistory" disabled>
                                        <option value="">{{__('Not defined')}}</option>
                                        @if (isset($toxicologicalConditions)&&count($toxicologicalConditions)>0)
                                            @foreach ($toxicologicalConditions as $item)
                                                <option value={{ $item->id }} @if(Auth::user()->toxicologicalCondition != null && $item->id == Auth::user()->toxicologicalCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="toxicologicalHistory">{{__('Toxicological History')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <select id="gynecoObstetricHistory" name="gynecoObstetricHistory" class="form-select" aria-label="gynecoObstetricHistory" disabled>
                                        <option value="">{{__('Not defined')}}</option>
                                        @if (isset($gynecoObstetricConditions)&&count($gynecoObstetricConditions)>0)
                                            @foreach ($gynecoObstetricConditions as $item)
                                                <option value={{ $item->id }} @if(Auth::user()->gynecoObstetricCondition != null && $item->id == Auth::user()->gynecoObstetricCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="gynecoObstetricHistory">{{__('Gyneco Obstetric History')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <select id="medications" name="medications" class="form-select" aria-label="medications" disabled>
                                        <option value="">{{__('Not defined')}}</option>
                                        @if (isset($medicationConditions)&&count($medicationConditions)>0)
                                            @foreach ($medicationConditions as $item)
                                                <option value={{ $item->id }} @if(Auth::user()->medicationCondition != null && $item->id == Auth::user()->medicationCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="medications">{{__('Are you taking any of these medications?')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <select id="dietaryHistory" name="dietaryHistory" class="form-select" aria-label="dietaryHistory" disabled>
                                        <option value="">{{__('Not defined')}}</option>
                                        @if (isset($dietaryConditions)&&count($dietaryConditions)>0)
                                            @foreach ($dietaryConditions as $item)
                                                <option value={{ $item->id }} @if(Auth::user()->dietaryCondition != null && $item->id == Auth::user()->dietaryCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="dietaryHistory">{{__('Dietary History')}}</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <input id="surgery" type="text" placeholder="{{__('Do you have any surgery?')}}" class="form-control" name="surgery" value="{{ Auth::user()->surgery ?? '' }}" disabled>
                                    <label for="surgery">{{__('Do you have any surgery?')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection