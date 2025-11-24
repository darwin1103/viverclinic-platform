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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobTrailingController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\QualifyStaffController;
use App\Http\Controllers\RecomentationsController;
use App\Http\Controllers\ReferralsController;
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

require __DIR__.'/client.php';
require __DIR__.'/admin.php';
require __DIR__.'/staff.php';
