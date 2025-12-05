<?php

use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminScheduleAppointmentController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContractedTreatmentController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TreatmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:SUPER_ADMIN|OWNER'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('treatment', TreatmentController::class);

    Route::post('/role/assignPermission',[RoleController::class,'assignPermission'])->name('role.assign.permission');
    Route::post('/role/removePermission',[RoleController::class,'removePermission'])->name('role.remove.permission');
    Route::post('/role/assignUser',[RoleController::class,'assignUser'])->name('role.assign.user');
    Route::post('/role/removeUser',[RoleController::class,'removeUser'])->name('role.remove.user');
    Route::resource('role', RoleController::class);

    Route::get('/permission/getPermissionsList/{id}',[PermissionController::class,'getPermissionsList'])->name('permission.list');
    Route::resource('permission', PermissionController::class);

    Route::resource('client', ClientController::class);
    Route::get('/client/getUsers/{id}',[ClientController::class,'getusers'])->name('client.list');

    Route::resource('branch', BranchController::class);

    Route::resource('staff', StaffController::class);

    Route::resource('owner', OwnerController::class);

    Route::resource('contracted-treatment', ContractedTreatmentController::class);

    // Appointments Management
    Route::controller(AdminAppointmentController::class)->group(function () {
        // Main view
        Route::get('/appointments', 'index')->name('appointments.index');

        // Fetch appointments for date range (AJAX)
        Route::post('/appointments/fetch', 'fetch')->name('appointments.fetch');

        // Mark as attended
        Route::post('/appointments/{appointment}/mark-attended', 'markAsAttended')
            ->name('appointments.mark-attended');

        // Confirm appointment
        Route::post('/appointments/{appointment}/confirm', 'confirm')
            ->name('appointments.confirm');

        // Reschedule appointment
        Route::post('/appointments/{appointment}/reschedule', 'reschedule')
            ->name('appointments.reschedule');

        // Cancel appointment
        Route::post('/appointments/{appointment}/cancel', 'cancel')
            ->name('appointments.cancel');

        // Get available slots (for rescheduling)
        Route::post('/appointments/available-slots', 'availableSlots')
            ->name('appointments.available-slots');

        // Update appointment (generic endpoint)
        Route::put('/appointments/{appointment}', 'update')
            ->name('appointments.update');

        // Update appointment (generic endpoint)
        Route::put('/appointments/{appointment}', 'assignStaffSequentially')
            ->name('appointments.assign-staff');
    });

    // Staff list for filters
    Route::get('/users-staff/list', [AdminAppointmentController::class, 'getStaffList'])
        ->name('staff.list');

    // Treatments list for filters
    Route::get('/all-treatments/list', [AdminAppointmentController::class, 'getTreatmentsList'])
        ->name('treatments.list');

    Route::get('/contrated_treatment/{contracted_treatment}/schedule-appointment', [AdminScheduleAppointmentController::class, 'index'])
        ->name('schedule-appointment.index');

    Route::post('/schedule-appointment/store', [AdminScheduleAppointmentController::class, 'store'])
        ->name('schedule-appointment.store');

    Route::post('/schedule-appointment/{appointment}/rate', [AdminScheduleAppointmentController::class, 'rate'])
        ->name('schedule-appointment.rate');

    Route::post('/schedule-appointment/{appointment}/resched', [AdminScheduleAppointmentController::class, 'resched'])
        ->name('schedule-appointment.resched');

    Route::post('/schedule-appointment/{appointment}/confirm', [AdminScheduleAppointmentController::class, 'confirm'])
        ->name('schedule-appointment.confirm');

    Route::post('/schedule-appointment/{appointment}/cancel', [AdminScheduleAppointmentController::class, 'cancel'])
        ->name('schedule-appointment.cancel');

    Route::post('/appointments/available-slots', [AdminScheduleAppointmentController::class, 'availableSlots'])
        ->name('schedule-appointment.available-slots');

    // Rutas CRUD de Activos
    Route::resource('assets', AssetController::class);

    // Ruta para modificar stock específicamente (Modal)
    Route::post('assets/{asset}/stock', [AssetController::class, 'updateStock'])->name('assets.stock.update');

    // Rutas para Notas (dentro de assets)
    Route::post('assets/{asset}/notes', [AssetController::class, 'storeNote'])->name('assets.notes.store');
    Route::delete('assets/notes/{note}', [AssetController::class, 'destroyNote'])->name('assets.notes.destroy');
    Route::put('assets/notes/{note}', [AssetController::class, 'updateNote'])->name('assets.notes.update');

    Route::resource('products', AdminProductController::class);

    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

});
