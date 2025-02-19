<?php

use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\ActivationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::resource('plugins', PluginController::class);
    Route::get('plugins/{plugin}/releases', [\App\Http\Controllers\Admin\PluginController::class, 'releases'])
        ->name('plugins.releases');
    Route::resource('licenses', LicenseController::class);
    Route::resource('activations', ActivationController::class);
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__ . '/auth.php';
