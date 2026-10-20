<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Produção: links sempre em HTTPS e bloqueio de migrate:fresh/db:wipe acidentais.
        URL::forceHttps($this->app->isProduction());
        DB::prohibitDestructiveCommands($this->app->isProduction());

        // Em desenvolvimento, acusa consultas N+1 e atributos inexistentes (RNF07).
        Model::shouldBeStrict(! $this->app->isProduction());

        // Política de senha de OSCs e administradores (RNF05).
        Password::defaults(fn () => Password::min(8)->letters()->numbers());

        // Formulário público sem login: limita envios por IP (RF05).
        RateLimiter::for('manifestacoes', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }
}
