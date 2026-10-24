<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Na API, respostas (inclusive erros e validação) são sempre JSON,
 * mesmo que o cliente não envie `Accept: application/json`.
 */
class ForcarRespostaJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
