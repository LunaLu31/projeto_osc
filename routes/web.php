<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\ManifestacaoInteresseController;

Route::get('/', [DemandaController::class, 'index']);
Route::post('/demandas', [DemandaController::class, 'store']);
Route::post('/manifestar', [ManifestacaoInteresseController::class, 'store']);
