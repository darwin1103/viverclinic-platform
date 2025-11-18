<?php

use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContractedTreatmentController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\AgendaDayController;
use App\Http\Controllers\AgendaNewController;
use App\Http\Controllers\CancelAppointmentController;
use App\Http\Controllers\CareTipsController;
use App\Http\Controllers\Client\ContractedTreatmentController as ClientContractedTreatmentController;
use App\Http\Controllers\Client\SaveInformedConsentController;
use App\Http\Controllers\Client\ScheduleAppointmentController;
use App\Http\Controllers\Client\TreatmentController as ClientTreatmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobTrailingController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\QualifyStaffController;
use App\Http\Controllers\RecomentationsController;
use App\Http\Controllers\ReferralsController;
use App\Http\Controllers\Staff\StaffAppointmentController;
use App\Http\Controllers\UserRegistrationByBranchController;
use App\Http\Controllers\VirtualWalletController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/{branch}/registro', UserRegistrationByBranchController::class)->name('registration-by-branch.create');

Route::post('/profile/uploadProfilePhoto',[ProfileController::class,'uploadProfilePhoto']);
Route::resource('profile', ProfileController::class);

Route::resource('medical-record', MedicalRecordController::class);
Route::resource('qualify-staff', QualifyStaffController::class);
Route::resource('care-tips', CareTipsController::class);
Route::resource('virtual-wallet', VirtualWalletController::class);
Route::resource('promotions', PromotionsController::class);
Route::resource('recomentations', RecomentationsController::class);
Route::resource('referrals', ReferralsController::class);
Route::resource('cancel-appointment', CancelAppointmentController::class);
Route::resource('agenda-day', AgendaDayController::class);
Route::resource('agenda-new', AgendaNewController::class);
Route::resource('job-trailing', JobTrailingController::class);

Route::middleware(['auth', 'verified', 'role:EMPLOYEE'])->prefix('staff')->name('staff.')->group(function () {

    Route::controller(StaffAppointmentController::class)->group(function () {

        // Main view
        Route::get('/appointment', 'index')->name('appointment.index');

    });

});

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

});

// Rutas para Pacientes
Route::middleware(['auth', 'verified', 'role:PATIENT'])->name('client.')->group(function () {

    Route::resource('/treatment', ClientTreatmentController::class);
    Route::resource('/contracted-treatment', ClientContractedTreatmentController::class);
    Route::resource('/informed-consent', SaveInformedConsentController::class);

    Route::get('/contrated_treatment/{contracted_treatment}/schedule-appointment', [ScheduleAppointmentController::class, 'index'])
        ->name('schedule-appointment.index');

    Route::post('/schedule-appointment/store', [ScheduleAppointmentController::class, 'store'])
        ->name('schedule-appointment.store');

    Route::post('/schedule-appointment/{appointment}/rate', [ScheduleAppointmentController::class, 'rate'])
        ->name('schedule-appointment.rate');

    Route::post('/schedule-appointment/{appointment}/resched', [ScheduleAppointmentController::class, 'resched'])
        ->name('schedule-appointment.resched');

    Route::post('/schedule-appointment/{appointment}/confirm', [ScheduleAppointmentController::class, 'confirm'])
        ->name('schedule-appointment.confirm');

    Route::post('/schedule-appointment/{appointment}/cancel', [ScheduleAppointmentController::class, 'cancel'])
        ->name('schedule-appointment.cancel');

    Route::post('/appointments/available-slots', [ScheduleAppointmentController::class, 'availableSlots'])
        ->name('schedule-appointment.available-slots');



});
