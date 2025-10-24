@props(['daysOfWeek', 'schedules' => null, 'enabled' => true])

<div class="mb-3">
    <h4 class="my-3">Horario de Trabajo</h4>
    @foreach ($daysOfWeek as $day)
        <div class="row align-items-center py-2 border-top">
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <div class="form-check">
                    <input class="form-check-input day-checkbox" type="checkbox" id="cb_{{ $day }}" data-day="{{ $day }}" {{ $schedules && $schedules->has($day) ? 'checked' : '' }} required @if (!$enabled) disabled @endif>
                    <label class="form-check-label" for="cb_{{ $day }}">
                        {{ $day }}
                    </label>
                </div>
            </div>
            <div class="col-12 col-md-10 schedule-container" data-day="{{ $day }}">
                @if ($schedules && $schedules->has($day))
                    @foreach ($schedules[$day] as $index => $schedule)
                        <div class="row py-1 align-items-end time-block ">
                            <div class="col-5">
                                <label class="form-check-label">
                                    Inicio
                                </label>
                                <input type="time" class="form-control" name="schedules[{{ $day }}][{{$index}}][start_time]" value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required @if (!$enabled) disabled @endif>
                            </div>
                            <div class="col-5">
                                <label class="form-check-label">
                                    Fin
                                </label>
                                <input type="time" class="form-control" name="schedules[{{ $day }}][{{$index}}][end_time]" value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required @if (!$enabled) disabled @endif>
                            </div>
                            @if ($enabled)
                            <div class="col-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-time-block"><i class="bi bi-trash"></i></button>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted no-work-day mb-0">Día no laborable.</p>
                @endif
            </div>
            @if ($enabled)
             <div class="col-12 text-md-end mt-2">
                 <button type="button" class="btn btn-outline-primary btn-sm add-time-block" data-day="{{ $day }}" style="{{ $schedules && $schedules->has($day) ? '' : 'display: none;' }}"><i class="bi bi-plus-circle"></i> Agregar Bloque</button>
            </div>
            @endif
        </div>
    @endforeach
</div>
