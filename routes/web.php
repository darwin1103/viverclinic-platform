<?php

use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AgendaDayController;
use App\Http\Controllers\AgendaNewController;
use App\Http\Controllers\CancelAppointmentController;
use App\Http\Controllers\CareTipsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobTrailingController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\QualifyStaffController;
use App\Http\Controllers\RecomentationsController;
use App\Http\Controllers\ReferralsController;
use App\Http\Controllers\ScheduleAppointmentController;
use App\Http\Controllers\UserRegistrationByBranchController;
use App\Http\Controllers\VirtualWalletController;
use App\Http\Controllers\Patient\BuyTreatmentController;
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

Route::post('/client/save/informed/consent',[ClientController::class,'saveInformedConsent'])->name('client.informed.consent');

Route::post('/profile/uploadProfilePhoto',[ProfileController::class,'uploadProfilePhoto']);
Route::resource('profile', ProfileController::class);

Route::resource('medical-record', MedicalRecordController::class);
Route::resource('qualify-staff', QualifyStaffController::class);
Route::resource('care-tips', CareTipsController::class);
Route::resource('virtual-wallet', VirtualWalletController::class);
Route::resource('promotions', PromotionsController::class);
Route::resource('recomentations', RecomentationsController::class);
Route::resource('referrals', ReferralsController::class);
Route::resource('schedule-appointment', ScheduleAppointmentController::class);
Route::resource('cancel-appointment', CancelAppointmentController::class);
Route::resource('agenda-day', AgendaDayController::class);
Route::resource('agenda-new', AgendaNewController::class);
Route::resource('job-trailing', JobTrailingController::class);

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Proteger el CRUD con el middleware de permisos de Spatie
    Route::middleware(['can:owner_dashboard_treatment_management,admin_dashboard_treatment_management'])
        ->resource('treatment', TreatmentController::class);

    Route::post('/role/assignPermission',[RoleController::class,'assignPermission'])->name('role.assign.permission');
    Route::post('/role/removePermission',[RoleController::class,'removePermission'])->name('role.remove.permission');
    Route::post('/role/assignUser',[RoleController::class,'assignUser'])->name('role.assign.user');
    Route::post('/role/removeUser',[RoleController::class,'removeUser'])->name('role.remove.user');
    Route::resource('role', RoleController::class);

    Route::get('/permission/getPermissionsList/{id}',[PermissionController::class,'getPermissionsList'])->name('permissions.list');
    Route::resource('permission', PermissionController::class);

    Route::resource('client', ClientController::class);
    Route::get('/client/getUsers/{id}',[ClientController::class,'getusers'])->name('client.list');

    Route::resource('branch', BranchController::class);

    Route::resource('staff', StaffController::class);

    Route::resource('owner', OwnerController::class);

});

// Rutas para Pacientes
Route::middleware(['auth', 'verified', 'can:patient_treatment_home_btn'])->name('patient.')->group(function () {

    // Muestra los tratamientos de una sucursal específica
    Route::get('/buy-treatment', [BuyTreatmentController::class, 'index'])->name('buy-treatment.index');

    Route::get('/buy-treatment/{treatment}', [BuyTreatmentController::class, 'show'])->name('buy-treatment.show');

});
