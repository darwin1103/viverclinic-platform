<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
Route::resource('users', UserController::class);

Route::resource('branches', BranchController::class);

Route::resource('profile', ProfileController::class);
