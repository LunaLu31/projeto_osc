<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\NewAccessToken;

/**
 * Emissão e revogação de tokens Bearer (Laravel Sanctum) para OSCs e administradores.
 * A "ability" do token é o próprio perfil (`osc` ou `admin`) e define quais rotas ele acessa.
 */
trait EmiteTokens
{
    /**
     * @return array{0: Authenticatable, 1: NewAccessToken, 2: Carbon}
     */
    protected function emitirToken(LoginRequest $request, string $perfil): array
    {
        $usuario = $request->validarCredenciais($perfil);
        $expiraEm = now()->addMinutes(config('sanctum.expiration'));
        $token = $usuario->createToken($request->input('dispositivo') ?: 'api', [$perfil], $expiraEm);

        return [$usuario, $token, $expiraEm];
    }

    protected function revogarTokenAtual(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
