<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Viverclinic') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container-fluid px-2 px-md-3 px-lg-5">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo-viverclinic.png') }}" alt="{{ config('app.name', 'Viverclinic') }}" style="max-height: 45px; width: auto;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <img alt="photo profile" width="32px" height="32px" class="rounded-circle navbar-photo me-2" src="{{ Auth::user()->photo_profile ? asset(Storage::url(Auth::user()->photo_profile)) : asset('images/icons/default-avatar.svg') }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=32&background=6c757d&color=fff'">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item show-spinner" href="{{ route('profile.index') }}">
                                        <i class="bi bi-person-circle"></i>&nbsp;&nbsp;&nbsp;{{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-left"></i>&nbsp;&nbsp;&nbsp;{{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container-fuid py-3 px-2 px-md-3 px-lg-5">
            @if(
                !Route::is('dashboard') &&
                !Route::is('login') &&
                !Route::is('register') &&
                !Route::is('registration-by-branch.create') &&
                !Route::is('client.informed-consent.create')
            )
            <div class="">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i>
                    Volver al dashboard
                </a>
            </div>
            @endif
            @yield('content')
        </main>
    </div>
    @include('common.login-modal')
    @include('common.commons-js-functions')
    @stack('scripts')
</body>
</html>
