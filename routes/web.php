<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\ManifestacaoInteresseController;
use App\Http\Controllers\OscAuthController;

Route::get('/', [DemandaController::class, 'index']);
Route::post('/demandas', [DemandaController::class, 'store']);
Route::post('/manifestar', [ManifestacaoInteresseController::class, 'store']);
Route::post('/osc/login', [OscAuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/osc/logout', [OscAuthController::class, 'logout']);
