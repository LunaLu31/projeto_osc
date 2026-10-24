<?php

namespace App\Http\Middleware;

use App\Auth\TokensJwt;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rotas protegidas da API (uso: ->middleware('jwt:osc') ou 'jwt:admin').
 * Sem token ou com token inválido: 401. Com token válido de outro perfil: 403.
 */
class AutenticarApiJwt
{
    public function __construct(private TokensJwt $tokens) {}

    public function handle(Request $request, Closure $next, string $perfil): Response
    {
        $guard = "api_{$perfil}";

        if (Auth::guard($guard)->check()) {
            // $request->user() nos controllers passa a ser o dono do token.
            Auth::shouldUse($guard);

            return $next($request);
        }

        // Token válido, mas de outro perfil (ex.: token de OSC numa rota de administração). O guard
        // do perfil do token confere tudo (usuário existe, senha não mudou); senão, segue 401.
        $claims = $this->tokens->ler($request->bearerToken());

        if ($claims !== null && Auth::guard("api_{$claims->perfil}")->check()) {
            throw new AuthorizationException;
        }

        throw new AuthenticationException('Unauthenticated.', [$guard]);
    }
}
