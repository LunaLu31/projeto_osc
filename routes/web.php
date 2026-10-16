<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\ManifestacaoInteresseController;
use App\Http\Controllers\AdminPortalController;
use App\Http\Controllers\OscAuthController;
use App\Http\Controllers\OscController;

Route::get('/', [DemandaController::class, 'index']);
Route::post('/demandas', [DemandaController::class, 'store']);
Route::post('/manifestar', [ManifestacaoInteresseController::class, 'store']);
Route::post('/osc/login', [OscAuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/osc/logout', [OscAuthController::class, 'logout']);
Route::get('/admin', [AdminPortalController::class, 'entry']);
Route::get('/admin/setup', [AdminPortalController::class, 'setup']);
Route::post('/admin/setup', [AdminPortalController::class, 'storeFirstAdmin'])->middleware('throttle:5,1');
Route::post('/admin/login', [AdminPortalController::class, 'login'])->middleware('throttle:5,1');
Route::get('/admin/dashboard', [AdminPortalController::class, 'dashboard']);
Route::post('/admin/oscs', [OscController::class, 'store']);
Route::post('/admin/logout', [AdminPortalController::class, 'logout']);
