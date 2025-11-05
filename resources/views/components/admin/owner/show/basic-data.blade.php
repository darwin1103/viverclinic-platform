@props([
'owner',
'schedules',
'branches',
'daysOfWeek',
])

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-body m-0 m-lg-3">
                 @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row">

                    <h4>Datos</h4>
                    <div class="col-12 col-md-6 my-2">
                        <div class="form-floating">
                            <input id="name" type="text" placeholder="Nombre Completo" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $owner->name) }}" disabled>
                            <label for="name">Nombre Completo</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-md-6 my-2">
                        <div class="form-floating">
                            <input id="email" type="email" placeholder="Correo Electrónico" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $owner->email) }}" disabled>
                            <label for="email">Correo Electrónico</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
