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

    Route::get('/payments', [\App\Http\Controllers\Staff\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/packages', [\App\Http\Controllers\Staff\PackageController::class, 'index'])->name('packages.index');
    Route::get('/trainings', [\App\Http\Controllers\Staff\TrainingController::class, 'index'])->name('trainings.index');
    Route::get('/payroll', [\App\Http\Controllers\Staff\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/reports', [\App\Http\Controllers\Staff\ReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [\App\Http\Controllers\Staff\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/password', [\App\Http\Controllers\Staff\SettingController::class, 'updatePassword'])->name('settings.password');

});
