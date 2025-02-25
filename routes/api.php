<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::match(['get', 'post'], '/license/verify', [LicenseApiController::class, 'verify']);
Route::match(['get', 'post'], '/license/validate', [LicenseApiController::class, 'validateLicense']);
