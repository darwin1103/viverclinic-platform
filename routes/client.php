<?php

use App\Http\Controllers\Client\ContractedTreatmentController as ClientContractedTreatmentController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\SaveInformedConsentController;
use App\Http\Controllers\Client\ScheduleAppointmentController;
use App\Http\Controllers\Client\ShopController;
use App\Http\Controllers\Client\TreatmentController as ClientTreatmentController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout'); // Previsualizar compra
    Route::post('/shop/place-order', [ShopController::class, 'placeOrder'])->name('shop.placeOrder'); // Confirmar y guardar

    // Historial de Compras
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}', [OrderController::class, 'show'])->name('orders.show');

});

Route::post('/appointments/available-slots', [ScheduleAppointmentController::class, 'availableSlots'])
    ->name('schedule-appointment.available-slots');
