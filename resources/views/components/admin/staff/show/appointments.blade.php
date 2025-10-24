@props(['appointments', 'title' => ''])

<div class="container my-4">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center">
            <div class="card w-100">
                <div class="card-body m-0 m-lg-3">
                    <div class="row d-flex justify-content-center align-items-center mb-2">
                        <div class="col-12 text-center">
                            {{-- Puedes incluir un logo si lo deseas --}}
                            {{-- <img alt="app logo" style="width: 150px;" class="rounded mb-3" src="{{ asset('path/to/your/logo.png') }}"> --}}
                            <div class="h2 my-3">
                                {{ $title }}
                            </div>
                        </div>
                    </div>

                    <x-admin.staff.show.appointments-table :appointments="$appointments" />

                </div>
            </div>
        </div>
    </div>
</div>
