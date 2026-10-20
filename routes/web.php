<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Osc;
use App\Http\Controllers\Publico;
use Illuminate\Support\Facades\Route;

/*
| Área pública (apoiadores, sem login)
*/
Route::get('/', Publico\FeedController::class)->name('feed');
Route::get('/demandas/{demanda}', [Publico\DemandaController::class, 'show'])->name('demandas.show');
Route::post('/demandas/{demanda}/interesse', [Publico\ManifestacaoInteresseController::class, 'store'])
    ->middleware('throttle:manifestacoes')
    ->name('demandas.interesse');
Route::get('/demandas/{demanda}/obrigado', [Publico\ManifestacaoInteresseController::class, 'obrigado'])
    ->name('demandas.obrigado');

/*
| Área da OSC (guard "osc")
*/
Route::prefix('osc')->name('osc.')->group(function () {
    Route::middleware('guest:osc')->group(function () {
        Route::get('/login', [Osc\AuthController::class, 'create'])->name('login');
        Route::post('/login', [Osc\AuthController::class, 'store']);
    });

    Route::middleware('auth:osc')->group(function () {
        Route::post('/logout', [Osc\AuthController::class, 'destroy'])->name('logout');
        Route::get('/painel', Osc\PainelController::class)->name('painel');

        Route::get('/demandas', [Osc\DemandaController::class, 'index'])->name('demandas.index');
        Route::get('/demandas/criar', [Osc\DemandaController::class, 'create'])->name('demandas.create');
        Route::post('/demandas', [Osc\DemandaController::class, 'store'])->name('demandas.store');
        Route::get('/demandas/{demanda}/editar', [Osc\DemandaController::class, 'edit'])->name('demandas.edit');
        Route::put('/demandas/{demanda}', [Osc\DemandaController::class, 'update'])->name('demandas.update');
        Route::patch('/demandas/{demanda}/status', [Osc\DemandaController::class, 'updateStatus'])->name('demandas.status');
        Route::delete('/demandas/{demanda}', [Osc\DemandaController::class, 'destroy'])->name('demandas.destroy');

        Route::get('/contatos', [Osc\ContatoController::class, 'index'])->name('contatos.index');

        Route::get('/senha', [Osc\SenhaController::class, 'edit'])->name('senha.edit');
        Route::put('/senha', [Osc\SenhaController::class, 'update'])->name('senha.update');
    });
});

/*
| Portal administrativo (guard "admin")
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'store']);
        Route::get('/configuracao', [Admin\ConfiguracaoController::class, 'create'])->name('configuracao');
        Route::post('/configuracao', [Admin\ConfiguracaoController::class, 'store'])->middleware('throttle:5,1');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', Admin\PainelController::class)->name('painel');

        Route::post('/oscs', [Admin\OscController::class, 'store'])->name('oscs.store');
        Route::get('/oscs/{osc}/editar', [Admin\OscController::class, 'edit'])->name('oscs.edit');
        Route::put('/oscs/{osc}', [Admin\OscController::class, 'update'])->name('oscs.update');
        Route::delete('/oscs/{osc}', [Admin\OscController::class, 'destroy'])->name('oscs.destroy');

        Route::patch('/demandas/{demanda}/status', [Admin\DemandaController::class, 'updateStatus'])->name('demandas.status');
        Route::delete('/demandas/{demanda}', [Admin\DemandaController::class, 'destroy'])->name('demandas.destroy');
    });
});
