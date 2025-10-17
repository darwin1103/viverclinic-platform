<div class="row justify-content-center my-2">
    <div class="col-12 col-md-8 mb-3">
        <div class="row gx-3 gy-3 my-2">
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Medical record') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Qualify staff') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Treatment') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Care tips') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Buy package') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Virtual wallet') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Promotions') }}</button>
            </div>
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Recommendations') }}</button>
            </div>
            
            <div class="col-12 col-md-4">
                <button type="button" class="btn btn-custom btn-custom-height">{{ __('Referrals') }}</button>
            </div>
        </div>
        <div class="row gx-3 gy-3 mt-2">
            <div class="col-12 col-md-6">
                <button type="button" class="btn btn-custom btn-schedule-appointment">{{ __('Schedule an appointment') }}</button>
            </div>
            <div class="col-12 col-md-6">
                <button type="button" class="btn btn-custom btn-cancel-appointment">{{ __('Cancel appointment') }}</button>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body px-4">
                    <div class="row">
                        <div class="col-3">
                            <img alt="photo profile" width="68px" height="68px" class="rounded-circle navbar-photo me-2" src="{{asset(Storage::url(Auth::user()->photo_profile?:config('app.app_default_img_profile')))}}">
                        </div>
                        <div class="col text-start">
                            <p class="fw-bold m-0">{{ __('Next Appointment') }}</p>
                            <p class="fs-3 fw-bold m-0 text-uppercase" style="color: #b6d3db;">N/A</p>
                        </div>
                    </div><hr>
                    <div class="row">
                        <div class="col text-start">
                            <p class="fw-bold m-0">{{ __('Balance') }}</p>
                            <p class="fs-1 fw-bold m-0 text-uppercase" style="color: #f9ffff;">$0.00</p>
                        </div>
                    </div><hr>
                    <div class="row">
                        <div class="col text-start">
                            <p class="fw-bold m-0">{{ __('Active Packages') }}</p>
                            <p class="fs-1 fw-bold m-0 text-uppercase" style="color: #b6d3db;">0</p>
                        </div>
                    </div><hr>
                    <div class="row">
                        <div class="col text-start">
                            <p class="fw-bold m-0">{{ __('Latest recommendations') }}</p>
                            <ul style="color: #b6d3db;">
                                <li>Elemento 1</li>
                                <li>Elemento 2</li>
                                <li>Elemento 3</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="card shadow">
                <div class="card-body px-4">
                    <h5 class="card-title fw-bold">{{ __('Treatment Progress') }}</h5>
                    <div class="progress my-4" role="progressbar" aria-label="Basic example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar w-75"></div>
                    </div>
                    <p class="fw-bold m-0" style="color: #33a1d6;">{{ __('') }}Progreso oratnicic</p>
                </div>
            </div>
        </div>
    </div>
</div>