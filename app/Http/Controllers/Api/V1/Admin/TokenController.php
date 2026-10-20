<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Concerns\EmiteTokens;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\V1\AdmResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

#[Group('Administração', 'Gestão das OSCs e moderação de todas as demandas da rede. Requer token de administrador.', weight: 3)]
class TokenController extends Controller
{
    use EmiteTokens;

    /**
     * Entrar (gerar token)
     *
     * Troca e-mail e senha do administrador por um token Bearer válido por 7 dias (RF01).
     *
     * @unauthenticated
     */
    public function store(LoginRequest $request): JsonResponse
    {
        [$admin, $token, $expiraEm] = $this->emitirToken($request, 'admin');

        return response()->json([
            'token' => $token->plainTextToken,
            'tipo' => 'Bearer',
            'expira_em' => $expiraEm->toIso8601String(),
            'admin' => AdmResource::make($admin),
        ], 201);
    }

    /**
     * Sair (revogar token)
     */
    public function destroy(Request $request): Response
    {
        $this->revogarTokenAtual($request);

        return response()->noContent();
    }
}
