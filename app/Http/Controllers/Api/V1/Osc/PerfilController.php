<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OscResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * Troca a senha inicial gerada pelo administrador. Todos os outros tokens da OSC são revogados.
     */
    public function senha(Request $request): Response
    {
        $dados = $request->validate([
            'senha_atual' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'confirmed', 'different:senha_atual', Password::defaults()],
        ]);

        $osc = $request->user();
        $osc->update(['senha_hash' => Hash::make($dados['password'])]);
        $osc->tokens()->whereKeyNot($osc->currentAccessToken()->getKey())->delete();

        return response()->noContent();
    }
}
