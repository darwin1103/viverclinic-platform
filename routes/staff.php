<?php

use App\Http\Controllers\Staff\StaffAppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:EMPLOYEE'])->prefix('staff')->name('staff.')->group(function () {

    Route::controller(StaffAppointmentController::class)->group(function () {

        // Main view
        Route::get('/appointment', 'index')
        ->name('appointment.index');

        Route::post('/set-appointment-shots/{appointment}', 'setAppointmnetShots')
            ->name('appointment.set-shots');

        Route::post('/mark-appointment-as-completed/{appointment}', 'markAppointmnetAsCompleted')
            ->name('appointment.mark-as-completed');

    });

});
