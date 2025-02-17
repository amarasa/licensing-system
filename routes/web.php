<?php

use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\ActivationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('admin')->group(function () {
    Route::resource('plugins', PluginController::class);
    Route::resource('licenses', LicenseController::class);
    Route::resource('activations', ActivationController::class);
});
