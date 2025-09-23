<div class="modal fade" style="z-index: 9999;" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body" style="position: relative;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 20px; right: 25px;"></button>
                <div class="container">
                    <div class="row text-center">
                        <div class="container-fluid">
                            <p class="fs-2 fw-bold center mb-2">{{__('Join')}}</p>
                            <form method="POST" action="{{route('login')}}">
                                @csrf
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <input id="email" placeholder="{{__('Email Address')}}" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{old('email')}}" autocomplete="email">
                                        <label for="email">{{__('Email Address')}}</label>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating passwordContainer">
                                        <input id="password" placeholder="{{__('Password')}}" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                                        <label for="email">{{__('Password')}}</label>
                                        <i class="fa-solid fa-eye toggleShowHidePassword" style="color: #b9b9b9;"></i>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check text-start">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{old('remember')?'checked':''}}>
                                            <label class="form-check-label" for="remember">{{__('Remember Me')}}</label>
                                            @if (Route::has('password.request'))
                                                <a class="btn btn-link float-end p-0" href="{{ route('password.request') }}">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('Login')}}
                                    </button>
                                    <div>
                                        <span>{{ __('No account yet?') }}</span>
                                        <span>
                                            <a class="btn btn-link show-spinner mb-1 p-0" href="{{ route('register') }}" role="button">
                                                {{ __('Create Account') }}
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
