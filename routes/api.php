<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Osc;
use App\Http\Controllers\Api\V1\Publico;
use Illuminate\Support\Facades\Route;

/*
| API REST v1 – prefixo /api/v1 (documentação em /docs/api)
| Autenticação: token Bearer do Laravel Sanctum, com "ability" igual ao perfil (osc/admin).
*/
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Público (sem login)
    Route::get('/demandas', [Publico\DemandaController::class, 'index'])->name('demandas.index');
    Route::get('/demandas/{demanda}', [Publico\DemandaController::class, 'show'])->name('demandas.show');
    Route::post('/demandas/{demanda}/interesses', [Publico\ManifestacaoInteresseController::class, 'store'])
        ->middleware('throttle:manifestacoes')
        ->name('demandas.interesses.store');
    Route::get('/rede/estatisticas', [Publico\RedeController::class, 'estatisticas'])->name('rede.estatisticas');
    Route::get('/rede/metadados', [Publico\RedeController::class, 'metadados'])->name('rede.metadados');

    // Área da OSC
    Route::prefix('osc')->name('osc.')->group(function () {
        Route::post('/tokens', [Osc\TokenController::class, 'store'])->name('tokens.store');

        Route::middleware(['auth:sanctum', 'abilities:osc'])->group(function () {
            Route::delete('/tokens/atual', [Osc\TokenController::class, 'destroy'])->name('tokens.destroy');
            Route::get('/perfil', [Osc\PerfilController::class, 'show'])->name('perfil.show');
            Route::put('/perfil/senha', [Osc\PerfilController::class, 'senha'])->name('perfil.senha');
            Route::get('/painel', Osc\PainelController::class)->name('painel');

            // PUT substitui todos os campos; PATCH altera só os enviados.
            Route::apiResource('demandas', Osc\DemandaController::class)->except('update');
            Route::put('/demandas/{demanda}', [Osc\DemandaController::class, 'update'])->name('demandas.update');
            Route::patch('/demandas/{demanda}', [Osc\DemandaController::class, 'atualizarParcial'])->name('demandas.patch');
            Route::patch('/demandas/{demanda}/status', [Osc\DemandaController::class, 'status'])->name('demandas.status');

            Route::get('/contatos', [Osc\ContatoController::class, 'index'])->name('contatos.index');
        });
    });

    // Administração
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/tokens', [Admin\TokenController::class, 'store'])->name('tokens.store');

        Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
            Route::delete('/tokens/atual', [Admin\TokenController::class, 'destroy'])->name('tokens.destroy');
            Route::get('/painel', Admin\PainelController::class)->name('painel');

            Route::apiResource('oscs', Admin\OscController::class)->except('update');
            Route::put('/oscs/{osc}', [Admin\OscController::class, 'update'])->name('oscs.update');
            Route::patch('/oscs/{osc}', [Admin\OscController::class, 'atualizarParcial'])->name('oscs.patch');

            Route::get('/demandas', [Admin\DemandaController::class, 'index'])->name('demandas.index');
            Route::get('/demandas/{demanda}', [Admin\DemandaController::class, 'show'])->name('demandas.show');
            Route::patch('/demandas/{demanda}/status', [Admin\DemandaController::class, 'status'])->name('demandas.status');
            Route::delete('/demandas/{demanda}', [Admin\DemandaController::class, 'destroy'])->name('demandas.destroy');
        });
    });
});
