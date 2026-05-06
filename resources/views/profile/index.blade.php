@extends('layouts.app')
@section('content')
@push('styles')
<style>
    .show-image-selector {
        cursor: pointer;
    }
    .profile-photo-container {
        position: absolute;
        top: 130px;
        left: 20px;
        border-radius: 50%;
        z-index: 5;
    }
    .photo-edit-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: white;
        border: 2px solid white;
        border-radius: 50%;
        color: #3a3a3a;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .profile-info-container {
        position: absolute;
        top: 185px;
        left: 140px;
        z-index: 5;
    }
    .profile-card-body {
        margin-top: 60px;
    }

    @media (max-width: 768px) {
        .profile-photo-container {
            left: 50%;
            transform: translateX(-50%);
            top: 100px;
        }
        .profile-info-container {
            position: relative;
            top: 0;
            left: 0;
            margin-top: 60px;
            text-align: center;
        }
        .profile-card-body {
            margin-top: 10px;
        }
    }
</style>
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>{{ __('Profile') }}</h1>
        </div>
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card shadow-sm w-100 w-md-75 w-lg-60" style="position: relative;">
                <div class="card-img-top" 
                    style="height: 180px; background: linear-gradient(135deg, #ff7e5f, #feb47b);">
                </div>
                
                {{-- Profile Photo --}}
                <div class="profile-photo-container show-image-selector">
                    <img id="profilePhoto" alt="user photo" width="100" height="100" class="rounded-circle border border-5 border-white" src="{{ Auth::user()->photo_profile ? asset(Storage::url(Auth::user()->photo_profile)) : asset('images/icons/default-avatar.svg') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=100&background=6c757d&color=fff'">
                    <i class="bi bi-camera-fill photo-edit-icon"></i>
                </div>
                
                <input type="file" id="profilePhotoInput" accept="image/png, image/jpeg, image/jpg" style="display:none;">
                
                {{-- User Info --}}
                <div class="profile-info-container">
                    <p class="fs-5 fw-semibold mb-0">
                        {{ Auth::user()->name ?? '' }}
                    </p>
                    <p class="fw-light mb-0" style="font-size: 12px;">{{__('Account created')}}: {{ Auth::user()->created_at->format('d-m-Y') ?? '' }}</p>
                </div>

                <div class="card-body profile-card-body">
                    <ul class="nav nav-underline nav-fill mb-3">
                        <li class="nav-item"><a class="nav-link active" aria-current="true" data-bs-toggle="tab" href="#basicInfo">{{__('Basic Information')}}</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#others">{{__('Others')}}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="basicInfo">
                            <form method="POST" class="row g-2" action="{{ route('profile.update',Auth::user()->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="col-12 col-lg-6">
                                    <div class="form-floating">
                                        <input id="name" type="text" placeholder="{{__('Name')}}" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ Auth::user()->name ?? '' }}">
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
                                        <input id="email" type="email" placeholder="{{__('Email Address')}}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email ?? '' }}" readonly disabled style="cursor: not-allowed;">
                                        <label for="email">{{__('Email Address')}}</label>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 date">
                                    <div class="form-floating">
                                        <input id="birthday" type="date" placeholder="{{__('Birthday')}}" class="form-control @error('birthday') is-invalid @enderror" name="birthday" value="{{ Auth::user()->birthday ?? '' }}">
                                        <label for="Birthday">{{__('Birthday')}}</label>
                                        @error('birthday')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-floating">
                                        <select id="genderSelect" name="genderSelect" class="form-select @error('genderSelect') is-invalid @enderror" aria-label="genderSelect">
                                            <option value="-1" @if (Auth::user()->gender == null) selected @endif>{{__('Select an option')}}</option>
                                            <option value="M" @if (Auth::user()->gender != null && Auth::user()->gender->code == 'M') selected @endif>{{__('Masculine')}}</option>
                                            <option value="F" @if (Auth::user()->gender != null && Auth::user()->gender->code == 'F') selected @endif>{{__('Female')}}</option>
                                        </select>
                                        <label for="genderSelect">{{__('Gender')}}</label>
                                        @error('genderSelect')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-block text-end">
                                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="others">
                            <div class="row my-3">
                                <div class="col-12 col-lg-6 my-1">
                                    <a class="btn btn-warning text-white w-100" href="{{route('password.request')}}">
                                        <i class="fa-solid fa-lock"></i>&nbsp;
                                        {{__('Change Password')}}
                                    </a>
                                </div>
                                <div class="col-12 col-lg-6 my-1">
                                    <button type="button" onclick="showDeleteConfirmation('{{Auth::user()->id}}')"
                                        @role('SUPER_ADMIN') class="btn btn-danger disabled w-100" @else class="btn btn-danger w-100" @endrole>
                                        <i class="fa-solid fa-trash-can"></i>&nbsp;
                                        @role('SUPER_ADMIN')
                                            {{__('Super Admin')}}
                                        @else
                                            {{__('Delete Account')}}
                                        @endrole
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('common.deleteConfirmationModal')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(".show-image-selector").on('click',function(){
            $("#profilePhotoInput").click();
        });
        $("#profilePhotoInput").on('change',function(e){
            const file = e.target.files[0];
            if (!file) return;
            const allowedTypes = ['image/jpeg','image/jpg','image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert("{{ __('Format not allowed. JPG, JPEG or PNG only') }}");
                return;
            }
            const formData = new FormData();
            formData.append('image', file);
            $.ajax({
                url: '/profile/uploadProfilePhoto',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    console.log(response);
                    if (response.profileURL) {
                        $("#profilePhoto").attr('src',response.profileURL);
                        $(".navbar-photo").attr('src',response.profileURL);
                    } else {
                        $("#profilePhoto").attr('src',"{{ asset('images/icons/default-avatar.svg') }}");
                        $(".navbar-photo").attr('src',"{{ asset('images/icons/default-avatar.svg') }}");
                    }
                    iziToast.success({
                        message: "{{ __('Successful operation') }}"
                    });
                },
                error: function(xhr, status, error){
                    ajaxErrorHandle(error);
                }
            });
        });
    }, false);
    function showDeleteConfirmation(elementID) {
        const modal = new bootstrap.Modal('#removeConfirmationModal');
        $('#delete').attr('action','{{url("/profile")}}'+'/'+elementID);
        $('#deleteElementBtn').attr('action','{{url("/profile")}}'+'/'+elementID);
        modal.show();
    };
</script>
@endpush
@endsection
