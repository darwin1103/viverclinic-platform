<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Viverclinic') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div id="app">
        <x-admin.dashboard.header :branches="$branches ?? null" />
        <x-admin.dashboard.sidebar />
        <x-admin.dashboard.modal-quick-add />
        <main class="content-area">
            @yield('content')
        </main>
    </div>
    @include('common.login-modal')
    @include('common.commons-js-functions')
    @stack('scripts')
</body>
</html>
