<?php

use App\Http\Controllers\AgendaDayController;
use App\Http\Controllers\AgendaNewController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BuyPackageController;
use App\Http\Controllers\CancelAppointmentController;
use App\Http\Controllers\CareTipsController;
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
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VirtualWalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/permissions/getPermissionsList/{roleUUID}',[PermissionController::class,'getPermissionsList'])->name('permissions.list');
Route::resource('permissions', PermissionController::class);

Route::get('/users/getUsers/{roleUUID}',[UserController::class,'getusers'])->name('users.list');
Route::post('/users/save/informed/consent',[UserController::class,'saveInformedConsent'])->name('users.informed.consent');
Route::resource('users', UserController::class);

Route::resource('branches', BranchController::class);

Route::post('/profile/uploadProfilePhoto',[ProfileController::class,'uploadProfilePhoto']);
Route::resource('profile', ProfileController::class);

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
