<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Api\V1\Concerns\EmiteTokens;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\V1\OscResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

#[Group('OSC', 'Área da organização: demandas próprias e contatos recebidos. Requer token de OSC.', weight: 2)]
class TokenController extends Controller
{
    use EmiteTokens;

    /**
     * Entrar (gerar token)
     *
     * Troca e-mail e senha da OSC por um token Bearer válido por 7 dias (RF01).
     * Após 5 tentativas erradas para o mesmo e-mail e IP, o acesso é bloqueado por 1 minuto.
     *
     * @unauthenticated
     */
    public function store(LoginRequest $request): JsonResponse
    {
        [$osc, $token, $expiraEm] = $this->emitirToken($request, 'osc');

        return response()->json([
            'token' => $token->plainTextToken,
            'tipo' => 'Bearer',
            'expira_em' => $expiraEm->toIso8601String(),
            'osc' => OscResource::make($osc),
        ], 201);
    }

    /**
     * Sair (revogar token)
     *
     * Invalida o token usado nesta requisição.
     */
    public function destroy(Request $request): Response
    {
        $this->revogarTokenAtual($request);

        return response()->noContent();
    }
}
