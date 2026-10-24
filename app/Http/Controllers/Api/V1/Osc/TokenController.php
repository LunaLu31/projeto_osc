<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\V1\OscResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

#[Group('OSC', 'Área da organização: demandas próprias e contatos recebidos. Requer token de OSC.', weight: 2)]
class TokenController extends Controller
{
    /**
     * Entrar (gerar token)
     *
     * Troca e-mail e senha da OSC por um JWT válido por 7 dias (RF01), a ser enviado no cabeçalho
     * `Authorization: Bearer {token}`. Após 5 tentativas erradas para o mesmo e-mail e IP, o acesso
     * é bloqueado por 1 minuto.
     *
     * @unauthenticated
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $osc = $request->validarCredenciais('osc');
        $token = Auth::guard('api_osc')->login($osc, lembrar: true);

        return response()->json([
            'token' => $token->token,
            'tipo' => 'Bearer',
            'expira_em' => $token->expiraEm->toIso8601String(),
            'osc' => OscResource::make($osc),
        ], 201);
    }

    /**
     * Renovar token
     *
     * Troca o token desta requisição por um novo, válido por mais 7 dias. O anterior é revogado.
     */
    public function renovar(): JsonResponse
    {
        $token = Auth::guard('api_osc')->trocarToken();

        return response()->json([
            'token' => $token->token,
            'tipo' => 'Bearer',
            'expira_em' => $token->expiraEm->toIso8601String(),
        ]);
    }

    /**
     * Sair (revogar token)
     *
     * Invalida o token usado nesta requisição.
     */
    public function destroy(): Response
    {
        Auth::guard('api_osc')->logout();

        return response()->noContent();
    }
}
