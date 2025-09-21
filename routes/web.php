<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/roles/assignPermission',[App\Http\Controllers\RoleController::class,'assignPermission'])->name('roles.assign.permission');
Route::post('/roles/removePermission',[App\Http\Controllers\RoleController::class,'removePermission'])->name('roles.remove.permission');
Route::resource('roles', RoleController::class);

Route::get('/permissions/getPermissionsList/{roleUUID}',[App\Http\Controllers\PermissionController::class,'getPermissionsList'])->name('permissions.list');
Route::resource('permissions', PermissionController::class);
