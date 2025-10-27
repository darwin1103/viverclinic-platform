@extends('layouts.app')
@section('content')
@push('styles')
<style>
    .informed-consent-title {
        padding: 5px 10px;
        border: 3px solid #fefefe;
        border-radius: 30px;
        font-size: 24px;
        font-weight: bold;
    }
</style>
@endpush
<div class="container">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-12 col-lg-4 text-center">
                            <img alt="app logo" style="width: 70%;" class="rounded" src="{{asset(Storage::url(config('app.app_img_logo')))}}">
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center align-items-center mb-2">
                        <div class="col-12 col-lg-4 text-center">
                            <div class="informed-consent-title my-3">
                                {{ __('Informed Consent') }}
                            </div>
                        </div>
                    </div>
                    <form method="POST" class="row g-2" action="{{ route('client.informed.consent') }}">
                        @csrf
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="name" type="text" placeholder="{{__('Full Name')}}" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ Auth::user()->name ?? '' }}">
                                <label for="name">{{__('Full Name')}}</label>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="citizenship" type="text" placeholder="{{__('Citizenship')}}" class="form-control @error('citizenship') is-invalid @enderror" name="citizenship" value="{{ Auth::user()->citizenship ?? '' }}">
                                <label for="citizenship">{{__('Citizenship')}}</label>
                                @error('citizenship')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="documentType" name="documentType" class="form-select @error('documentType') is-invalid @enderror" aria-label="documentType">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($documentTypes)&&count($documentTypes)>0)
                                        @foreach ($documentTypes as $item)
                                            <option value={{ $item->id }} @if(Auth::user()->documentType != null && Auth::user()->documentType->id == $item->id) selected @endif>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="documentType">{{__('Document Type')}}</label>
                                @error('documentType')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="documentNumber" type="text" placeholder="{{__('Document Number')}}" class="form-control @error('documentNumber') is-invalid @enderror" name="documentNumber" value="{{ Auth::user()->document_number ?? '' }}">
                                <label for="documentNumber">{{__('Document Number')}}</label>
                                @error('documentNumber')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating">
                                <input id="birthday" type="date" placeholder="{{__('Birthday')}}" class="form-control @error('birthday') is-invalid @enderror" name="birthday" value="{{ Auth::user()->birthday ?? '' }}">
                                <label for="birthday">{{__('Birthday')}}</label>
                                @error('birthday')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating">
                                <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" aria-label="gender">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($genres)&&count($genres)>0)
                                        @foreach ($genres as $gender)
                                            <option value={{ $gender->id }} @if(Auth::user()->gender != null && $gender->id == Auth::user()->gender->id) selected @endIf>{{ __($gender->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="gender">{{__('Gender')}}</label>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-floating">
                                <input id="profession" type="text" placeholder="{{__('Occupation or Profession')}}" class="form-control @error('profession') is-invalid @enderror" name="profession" value="{{ Auth::user()->profession ?? '' }}">
                                <label for="profession">{{__('Occupation or Profession')}}</label>
                                @error('profession')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="{{__('Cell Phone Number')}}" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ Auth::user()->phone ?? '' }}">
                                <label for="phone">{{__('Cell Phone Number')}}</label>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="email" type="email" placeholder="{{__('Email Address')}}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email ?? '' }}">
                                <label for="email">{{__('Email Address')}}</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input id="address" type="text" placeholder="{{__('Address')}}" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ Auth::user()->address ?? '' }}">
                                <label for="address">{{__('Address')}}</label>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="pathologicalHistory" name="pathologicalHistory" class="form-select @error('pathologicalHistory') is-invalid @enderror" aria-label="pathologicalHistory">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($pathologicalConditions)&&count($pathologicalConditions)>0)
                                        @foreach ($pathologicalConditions as $item)
                                            <option value={{ $item->id }} @if(Auth::user()->pathologicalCondition != null && $item->id == Auth::user()->pathologicalCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="pathologicalHistory">{{__('Pathological History')}}</label>
                                @error('pathologicalHistory')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="toxicologicalHistory" name="toxicologicalHistory" class="form-select @error('toxicologicalHistory') is-invalid @enderror" aria-label="toxicologicalHistory">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($toxicologicalConditions)&&count($toxicologicalConditions)>0)
                                        @foreach ($toxicologicalConditions as $item)
                                            <option value={{ $item->id }} @if(Auth::user()->toxicologicalCondition != null && $item->id == Auth::user()->toxicologicalCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="toxicologicalHistory">{{__('Toxicological History')}}</label>
                                @error('toxicologicalHistory')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="gynecoObstetricHistory" name="gynecoObstetricHistory" class="form-select @error('gynecoObstetricHistory') is-invalid @enderror" aria-label="gynecoObstetricHistory">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($gynecoObstetricConditions)&&count($gynecoObstetricConditions)>0)
                                        @foreach ($gynecoObstetricConditions as $item)
                                            <option value={{ $item->id }} @if(Auth::user()->gynecoObstetricCondition != null && $item->id == Auth::user()->gynecoObstetricCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="gynecoObstetricHistory">{{__('Gyneco Obstetric History')}}</label>
                                @error('gynecoObstetricHistory')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="medications" name="medications" class="form-select @error('medications') is-invalid @enderror" aria-label="medications">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($medicationConditions)&&count($medicationConditions)>0)
                                        @foreach ($medicationConditions as $item)
                                            <option value={{ $item->id }} @if(Auth::user()->medicationCondition != null && $item->id == Auth::user()->medicationCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="medications">{{__('Are you taking any of these medications?')}}</label>
                                @error('medications')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="dietaryHistory" name="dietaryHistory" class="form-select @error('dietaryHistory') is-invalid @enderror" aria-label="dietaryHistory">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($dietaryConditions)&&count($dietaryConditions)>0)
                                        @foreach ($dietaryConditions as $item)
                                            <option value={{ $item->id }} @if(Auth::user()->dietaryCondition != null && $item->id == Auth::user()->dietaryCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="dietaryHistory">{{__('Dietary History')}}</label>
                                @error('dietaryHistory')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <select id="treatment" name="treatment" class="form-select @error('treatment') is-invalid @enderror load-informed-consent-contract" aria-label="treatment">
                                    <option value="">{{__('Select an option')}}</option>
                                    @if (isset($treatmentConditions)&&count($treatmentConditions)>0)
                                        @foreach ($treatmentConditions as $item)
                                            <option data-contract-text="{{ $item->terms_conditions }}" value={{ $item->id }} @if(Auth::user()->treatmentCondition != null && $item->id == Auth::user()->treatmentCondition->id) selected @endIf>{{ __($item->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="treatment">{{__('What treatment are you going to have?')}}</label>
                                @error('treatment')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="surgery" type="text" placeholder="{{__('Do you have any surgery? Which one?')}}" class="form-control @error('surgery') is-invalid @enderror" name="surgery" value="{{ Auth::user()->surgery ?? '' }}">
                                <label for="surgery">{{__('Do you have any surgery? Which one?')}}</label>
                                @error('surgery')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating">
                                <input id="recommendation" type="text" placeholder="{{__('Did anyone recommend Viverclinic to you?')}}" class="form-control @error('recommendation') is-invalid @enderror" name="recommendation" value="{{ Auth::user()->recommendation ?? '' }}">
                                <label for="recommendation">{{__('Did anyone recommend Viverclinic to you?')}}</label>
                                @error('recommendation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input @error('termsConditions') is-invalid @enderror show-terms-conditions-modal" disabled type="checkbox" id="termsConditions" name="termsConditions" @if(Auth::user()->terms_conditions) checked @endif>
                                <label class="form-check-label" for="termsConditions">
                                    {{ __('I have clearly read the consent I accept terms and conditions') }}
                                </label>
                                @error('termsConditions')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notPregnant" name="notPregnant" @if(Auth::user()->not_pregnant) checked @endif>
                                <label class="form-check-label" for="notPregnant">
                                    {{ __('Im not pregnant') }}
                                </label>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-block text-center">
                            <button type="submit" class="btn btn-primary w-auto mt-2" id="nextInformedConsent" disabled>{{__('Next')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="termsConditionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="termsConditionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="termsConditionsModalLabel">{{ __('Terms and Conditions') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="acceptTermsConditions">{{ __('I accept') }}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(".load-informed-consent-contract").on('change',function() {
            $("#termsConditionsModal").find('.modal-body').empty();
            $('#termsConditions').prop('checked', false);
            $('#termsConditions').prop('disabled', true);
            $("#nextInformedConsent").prop('disabled', true);
            if ($(this).find('option:selected').val()!='') {
                $("#termsConditionsModal").find('.modal-body').html($(this).find('option:selected').attr('data-contract-text'));
                $("#termsConditions").removeAttr('disabled');
            }
        });
        $(".show-terms-conditions-modal").on('click',function() {
            if ($(this).is(':checked')) {
                const modal = new bootstrap.Modal('#termsConditionsModal');
                modal.show();
            }
        });
        $("#acceptTermsConditions").on('click',function() {
            $("#nextInformedConsent").removeAttr('disabled');
            bootstrap.Modal.getInstance('#termsConditionsModal').hide();
        });
    }, false);
</script>
@endpush
@endsection
