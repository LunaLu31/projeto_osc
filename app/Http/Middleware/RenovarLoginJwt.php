<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Site: renova o JWT de quem está usando o sistema, para o login só expirar depois de um
 * período sem acesso (veja JwtGuard::renovarSeNecessario). O cookie novo segue na resposta.
 */
class RenovarLoginJwt
{
    /** Guards do site, com o token em cookie. */
    private const GUARDS = ['osc', 'admin'];

    public function handle(Request $request, Closure $next): Response
    {
        $resposta = $next($request);

        foreach (self::GUARDS as $guard) {
            Auth::guard($guard)->renovarSeNecessario();
        }

        return $resposta;
    }
}
