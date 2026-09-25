<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['web', 'auth:admin']], function () {
    Route::get('/wallets', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallets.index');
    Route::get('/wallets/{id}', [\App\Http\Controllers\Admin\WalletController::class, 'show'])->name('wallets.show');
    Route::post('/wallets/{id}/add-points', [\App\Http\Controllers\Admin\WalletController::class, 'addPoints'])->name('wallets.add-points');
    Route::post('/wallets/{id}/deduct-points', [\App\Http\Controllers\Admin\WalletController::class, 'deductPoints'])->name('wallets.deduct-points');
});
