<?php

use App\Http\Controllers\StaffController;
use App\Http\Controllers\AgendaDayController;
use App\Http\Controllers\AgendaNewController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BuyPackageController;
use App\Http\Controllers\CancelAppointmentController;
use App\Http\Controllers\CareTipsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobTrailingController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\QualifyStaffController;
use App\Http\Controllers\RecomentationsController;
use App\Http\Controllers\ReferralsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleAppointmentController;
use App\Http\Controllers\VirtualWalletController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Patient\PatientTreatmentController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// Route::get('/welcome', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::post('/roles/assignPermission',[RoleController::class,'assignPermission'])->name('roles.assign.permission');
Route::post('/roles/removePermission',[RoleController::class,'removePermission'])->name('roles.remove.permission');
Route::post('/roles/assignUser',[RoleController::class,'assignUser'])->name('roles.assign.user');
Route::post('/roles/removeUser',[RoleController::class,'removeUser'])->name('roles.remove.user');
Route::resource('roles', RoleController::class);

Route::get('/permissions/getPermissionsList/{id}',[PermissionController::class,'getPermissionsList'])->name('permissions.list');
Route::resource('permissions', PermissionController::class);

Route::get('/client/getUsers/{id}',[ClientController::class,'getusers'])->name('client.list');
Route::post('/client/save/informed/consent',[ClientController::class,'saveInformedConsent'])->name('client.informed.consent');
Route::resource('client', ClientController::class);

Route::resource('branch', BranchController::class);

Route::post('/profile/uploadProfilePhoto',[ProfileController::class,'uploadProfilePhoto']);
Route::resource('profile', ProfileController::class);

Route::resource('staff', StaffController::class);

Route::resource('medical-record', MedicalRecordController::class);

Route::resource('qualify-staff', QualifyStaffController::class);

Route::resource('treatment', TreatmentController::class);

Route::resource('care-tips', CareTipsController::class);

Route::resource('buy-package', BuyPackageController::class);

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

});


// Rutas para Pacientes
Route::middleware(['auth', 'verified', 'can:patient_treatment_home_btn'])->group(function () {

    // Muestra los tratamientos de una sucursal específica
    Route::get('/branch/{branch}/treatment', [PatientTreatmentController::class, 'show'])->name('patient.treatment.show');

    // Procesa la selección de un tratamiento por parte del paciente
    Route::post('/contract-treatment', [PatientTreatmentController::class, 'store'])->name('patient.treatment.contract');

});
