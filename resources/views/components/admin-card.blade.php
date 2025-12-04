<div class="container">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    {{-- Logo Section (Opcional, basado en tu ejemplo) --}}
                    @if(isset($showLogo))
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-12 col-lg-4 text-center">
                            <img alt="app logo" style="width: 70%;" class="rounded" src="{{ asset(Storage::url(config('app.app_img_logo', 'default.png'))) }}">
                        </div>
                    </div>
                    @endif

                    {{-- Title Section --}}
                    <div class="row d-flex justify-content-center align-items-center mb-2">
                        <div class="col-12 text-center">
                            <h4 class="my-3">{{ $title }}</h4>
                        </div>
                    </div>

                    {{-- Content --}}
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
