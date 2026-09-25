<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminAuthController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('admin-users', \App\Http\Controllers\Admin\AdminUserController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/{id}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.status');
        Route::post('users/{id}/kyc', [\App\Http\Controllers\Admin\UserController::class, 'updateKyc'])->name('users.kyc');



        Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{id}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.status');
        
        Route::get('installations', [\App\Http\Controllers\Admin\InstallationController::class, 'index'])->name('installations.index');
        Route::get('installations/{id}', [\App\Http\Controllers\Admin\InstallationController::class, 'show'])->name('installations.show');
        Route::post('installations/{id}/status', [\App\Http\Controllers\Admin\InstallationController::class, 'updateStatus'])->name('installations.status');

        Route::get('leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/{id}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
        Route::post('leads/{id}/status', [\App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('leads.status');
        
        
        Route::get('redemptions', [\App\Http\Controllers\Admin\RedemptionRequestController::class, 'index'])->name('redemptions.index');
        Route::get('redemptions/{id}', [\App\Http\Controllers\Admin\RedemptionRequestController::class, 'show'])->name('redemptions.show');
        Route::post('redemptions/{id}/status', [\App\Http\Controllers\Admin\RedemptionRequestController::class, 'updateStatus'])->name('redemptions.status');
        
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class)->except(['edit', 'update', 'show']);
        
        Route::resource('cms-pages', \App\Http\Controllers\Admin\CmsPageController::class)->except(['show']);
        
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/users', [\App\Http\Controllers\Admin\ReportController::class, 'exportUsers'])->name('reports.export.users');
        Route::get('reports/export/invoices', [\App\Http\Controllers\Admin\ReportController::class, 'exportInvoices'])->name('reports.export.invoices');
        
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        
        Route::get('shops/download-sample', [\App\Http\Controllers\Admin\ShopController::class, 'downloadSample'])->name('shops.download-sample');
        Route::get('shops/export', [\App\Http\Controllers\Admin\ShopController::class, 'export'])->name('shops.export');
        Route::post('shops/import', [\App\Http\Controllers\Admin\ShopController::class, 'import'])->name('shops.import');
        Route::post('shops/bulk-delete', [\App\Http\Controllers\Admin\ShopController::class, 'bulkDelete'])->name('shops.bulk-delete');
        Route::resource('shops', \App\Http\Controllers\Admin\ShopController::class);
        
        Route::resource('zones', \App\Http\Controllers\Admin\ZoneController::class);
        Route::resource('states', \App\Http\Controllers\Admin\StateController::class);
        Route::resource('cities', \App\Http\Controllers\Admin\CityController::class);
        Route::resource('pincodes', \App\Http\Controllers\Admin\PincodeController::class);
        
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('subcategories', \App\Http\Controllers\Admin\SubcategoryController::class);
        Route::get('products/download-sample', [\App\Http\Controllers\Admin\ProductController::class, 'downloadSample'])->name('products.download-sample');
        Route::get('products/export', [\App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
        Route::post('products/import', [\App\Http\Controllers\Admin\ProductController::class, 'import'])->name('products.import');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

        Route::get('ajax/get-states', [\App\Http\Controllers\Admin\AjaxController::class, 'getStates'])->name('ajax.get-states');
        Route::get('ajax/get-districts', [\App\Http\Controllers\Admin\AjaxController::class, 'getDistricts'])->name('ajax.get-districts');
        Route::get('ajax/get-cities', [\App\Http\Controllers\Admin\AjaxController::class, 'getCities'])->name('ajax.get-cities');
        Route::get('ajax/get-pincodes', [\App\Http\Controllers\Admin\AjaxController::class, 'getPincodes'])->name('ajax.get-pincodes');
        Route::get('ajax/get-shops', [\App\Http\Controllers\Admin\AjaxController::class, 'getShopsByPincode'])->name('ajax.get-shops');
        Route::get('ajax/get-subcategories', [\App\Http\Controllers\Admin\AjaxController::class, 'getSubcategories'])->name('ajax.get-subcategories');
    });
});
