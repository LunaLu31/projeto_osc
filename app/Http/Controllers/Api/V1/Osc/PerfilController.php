<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OscResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Group('OSC', 'Área da organização: demandas próprias e contatos recebidos. Requer token de OSC.', weight: 2)]
class PerfilController extends Controller
{
    /**
     * Perfil da OSC autenticada
     */
    public function show(Request $request): OscResource
    {
        return OscResource::make($request->user()->loadCount('demandas'));
    }

    /**
     * Alterar senha
     *
     * Troca a senha inicial gerada pelo administrador. Todos os tokens da OSC deixam de valer,
     * inclusive o desta requisição: a resposta traz um token novo para continuar conectado.
     */
    public function senha(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'senha_atual' => ['required', 'current_password:api_osc'],
            'password' => ['required', 'confirmed', 'different:senha_atual', Password::defaults()],
        ]);

        $osc = $request->user();
        $osc->update(['senha_hash' => Hash::make($dados['password'])]);
        $token = Auth::guard('api_osc')->login($osc, lembrar: true);

        return response()->json([
            'token' => $token->token,
            'tipo' => 'Bearer',
            'expira_em' => $token->expiraEm->toIso8601String(),
        ]);
    }
}
