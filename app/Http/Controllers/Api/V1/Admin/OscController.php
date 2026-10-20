<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OscParcialRequest;
use App\Http\Requests\OscRequest;
use App\Http\Resources\V1\OscResource;
use App\Models\Osc;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Cadastro e gestão das OSCs pelo administrador (RF02/RF09).
 */
#[Group('Administração', 'Gestão das OSCs e moderação de todas as demandas da rede. Requer token de administrador.', weight: 3)]
class OscController extends Controller
{
    /**
     * Listar OSCs
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->validate([
            /** Busca por nome ou e-mail. */
            'busca' => ['nullable', 'string', 'max:100'],
            /** Itens por página (padrão 20). */
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $oscs = Osc::withCount('demandas')
            ->when($filtros['busca'] ?? null, fn ($q, $busca) => $q->where(fn ($q) => $q
                ->where('nome', 'like', "%{$busca}%")
                ->orWhere('email', 'like', "%{$busca}%")))
            ->orderBy('nome')
            ->paginate($filtros['por_pagina'] ?? 20)
            ->withQueryString();

        return OscResource::collection($oscs);
    }

    /**
     * Cadastrar OSC
     *
     * Se `password` não for enviado, uma senha inicial é gerada e devolvida **uma única vez**
     * em `senha_inicial`. Repasse-a por um canal seguro e oriente a troca no primeiro acesso.
     */
    public function store(OscRequest $request): JsonResponse
    {
        $senhaGerada = $request->validated('password') ? null : Str::password(12, symbols: false);

        $osc = $request->user()->oscs()->create($request->dadosOsc() + [
            'senha_hash' => Hash::make($request->validated('password') ?: $senhaGerada),
        ]);

        return response()->json([
            'data' => OscResource::make($osc->refresh()->loadCount('demandas')),
            /** Presente apenas quando a senha foi gerada pelo sistema. */
            'senha_inicial' => $senhaGerada,
        ], 201);
    }

    /**
     * Detalhar OSC
     */
    public function show(Osc $osc): OscResource
    {
        return OscResource::make($osc->loadCount('demandas'));
    }

    /**
     * Substituir OSC
     *
     * Atualização completa. Envie `password` e `password_confirmation` apenas para redefinir a senha
     * (isso também revoga os tokens da OSC).
     */
    public function update(OscRequest $request, Osc $osc): OscResource
    {
        return $this->salvar($request, $osc);
    }

    /**
     * Atualizar parte da OSC
     *
     * Atualização parcial: envie apenas os campos que deseja alterar.
     */
    public function atualizarParcial(OscParcialRequest $request, Osc $osc): OscResource
    {
        return $this->salvar($request, $osc);
    }

    private function salvar(OscRequest $request, Osc $osc): OscResource
    {
        $dados = $request->dadosOsc();

        if ($novaSenha = $request->validated('password')) {
            $dados['senha_hash'] = Hash::make($novaSenha);
            $osc->tokens()->delete();
        }

        $osc->update($dados);

        return OscResource::make($osc->refresh()->loadCount('demandas'));
    }

    /**
     * Excluir OSC
     *
     * Exclui também as demandas da OSC e as manifestações recebidas.
     */
    public function destroy(Osc $osc): Response
    {
        $osc->tokens()->delete();
        $osc->delete();

        return response()->noContent();
    }
}
