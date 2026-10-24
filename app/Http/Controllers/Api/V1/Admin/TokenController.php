<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\V1\AdmResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

#[Group('Administração', 'Gestão das OSCs e moderação de todas as demandas da rede. Requer token de administrador.', weight: 3)]
class TokenController extends Controller
{
    /**
     * Entrar (gerar token)
     *
     * Troca e-mail e senha do administrador por um JWT válido por 7 dias (RF01), a ser enviado no
     * cabeçalho `Authorization: Bearer {token}`.
     *
     * @unauthenticated
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $admin = $request->validarCredenciais('admin');
        $token = Auth::guard('api_admin')->login($admin, lembrar: true);

        return response()->json([
            'token' => $token->token,
            'tipo' => 'Bearer',
            'expira_em' => $token->expiraEm->toIso8601String(),
            'admin' => AdmResource::make($admin),
        ], 201);
    }

    /**
     * Renovar token
     *
     * Troca o token desta requisição por um novo, válido por mais 7 dias. O anterior é revogado.
     */
    public function renovar(): JsonResponse
    {
        $token = Auth::guard('api_admin')->trocarToken();

        return response()->json([
            'token' => $token->token,
            'tipo' => 'Bearer',
            'expira_em' => $token->expiraEm->toIso8601String(),
        ]);
    }

    /**
     * Sair (revogar token)
     */
    public function destroy(): Response
    {
        Auth::guard('api_admin')->logout();

        return response()->noContent();
    }
}
