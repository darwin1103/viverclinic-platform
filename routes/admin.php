<?php

use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminScheduleAppointmentController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContractedTreatmentController;
use App\Http\Controllers\Admin\ManualSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AdminManagerController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TreatmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'permission:admin_dashboard'])->prefix('admin')->name('admin.')->group(function () {

    Route::middleware('permission:admin_dashboard_treatment_management')->group(function () {
        Route::resource('treatment', TreatmentController::class);
        Route::resource('contracted-treatment', ContractedTreatmentController::class);
        Route::post('/treatment-order/{order}/approve', [ContractedTreatmentController::class, 'approvePayment'])->name('contracted-treatment.approve-payment');
        Route::post('/treatment-order/{order}/reject', [ContractedTreatmentController::class, 'rejectPayment'])->name('contracted-treatment.reject-payment');
    });

    Route::middleware('permission:admin_dashboard_role_management')->group(function () {
        Route::post('/role/assignPermission',[RoleController::class,'assignPermission'])->name('role.assign.permission');
        Route::post('/role/removePermission',[RoleController::class,'removePermission'])->name('role.remove.permission');
        Route::post('/role/assignUser',[RoleController::class,'assignUser'])->name('role.assign.user');
        Route::post('/role/removeUser',[RoleController::class,'removeUser'])->name('role.remove.user');
        Route::resource('role', RoleController::class);

        Route::get('/permission/getPermissionsList/{id}',[PermissionController::class,'getPermissionsList'])->name('permission.list');
        Route::resource('permission', PermissionController::class);
    });

    Route::middleware('permission:ver_pacientes')->group(function () {
        Route::resource('client', ClientController::class);
        Route::get('/client/getUsers/{id}',[ClientController::class,'getusers'])->name('client.list');
    });

    Route::middleware('permission:admin_dashboard_branch_management')->group(function () {
        Route::resource('branch', BranchController::class);
    });
    Route::post('/switch-branch', [BranchController::class, 'switchGlobalBranch'])->name('switch-branch');

    Route::middleware('permission:admin_dashboard_user_management')->group(function () {
        Route::resource('staff', StaffController::class);
        Route::resource('owner', OwnerController::class);
        Route::resource('admin-manager', AdminManagerController::class)->except(['show']);
    });

    // Appointments Management
    Route::middleware('permission:crear_citas')->group(function () {
        Route::controller(AdminAppointmentController::class)->group(function () {
            // Main view
            Route::get('/appointments', 'index')->name('appointments.index');

            // Create appointment manually
            Route::get('/appointments/create', 'create')->name('appointments.create');
            Route::post('/appointments', 'store')->name('appointments.store');

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
    });

    Route::middleware('permission:ver_inventario')->group(function () {
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

        // Rutas para Ventas Manuales
        Route::get('/manual-sales', [ManualSaleController::class, 'index'])->name('manual-sales.index');
        Route::get('/manual-sales/products', [ManualSaleController::class, 'products'])->name('manual-sales.products'); // AJAX HTML
        Route::get('/manual-sales/patients', [ManualSaleController::class, 'patients'])->name('manual-sales.patients'); // AJAX JSON
        Route::post('/manual-sales', [ManualSaleController::class, 'store'])->name('manual-sales.store');

        Route::get('orders/{order}/receipt', [OrderController::class, 'downloadReceipt'])->name('orders.receipt');

        // Payments (Admin)
        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/pending', [\App\Http\Controllers\Admin\PaymentController::class, 'pending'])->name('payments.pending');
        Route::get('/payments/create', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/{order}/approve', [\App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{order}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/payments/export', [\App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');
        
        // Appointments reschedule list
        Route::get('/appointments/reschedule-list', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'rescheduleList'])->name('appointments.reschedule-list');
    });

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Reports (Admin)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');

    Route::post('/treatment-order/{order}/approve', [ContractedTreatmentController::class, 'approvePayment'])->name('contracted-treatment.approve-payment');
    Route::post('/treatment-order/{order}/reject', [ContractedTreatmentController::class, 'rejectPayment'])->name('contracted-treatment.reject-payment');

    // Marketing Management
    Route::get('/referrals-report', [\App\Http\Controllers\Admin\ReferralReportController::class, 'index'])->name('referrals-report.index');
    Route::resource('promotions', \App\Http\Controllers\Admin\PromotionController::class);

    // Accounting Management (ADMIN sees own branch, SUPER_ADMIN sees all)
    Route::get('/accounting', [\App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('accounting.index');
    Route::post('/accounting', [\App\Http\Controllers\Admin\AccountingController::class, 'store'])->name('accounting.store');

    // Expense Categories Management
    Route::resource('expense-categories', \App\Http\Controllers\Admin\ExpenseCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Virtual Wallet Management
    Route::post('/virtual-wallet/{user}/add-balance', [\App\Http\Controllers\VirtualWalletController::class, 'addBalance'])->name('virtual-wallet.add-balance');

    // Gestión de Referidos (Local)
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

    // Payroll Management (SUPER_ADMIN/OWNER only)
    Route::middleware('role:SUPER_ADMIN|OWNER')->group(function () {
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::post('/payroll/{settlement}/mark-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
    });

});
