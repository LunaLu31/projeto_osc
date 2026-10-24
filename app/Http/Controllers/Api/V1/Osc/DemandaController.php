<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DemandaParcialRequest;
use App\Http\Requests\DemandaRequest;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * CRUD das demandas da OSC autenticada (RF03) e alteração de status (RF07).
 * A DemandaPolicy impede o acesso a demandas de outras OSCs (403).
 */
#[Group('OSC', 'Área da organização: demandas próprias e contatos recebidos. Requer token de OSC.', weight: 2)]
class DemandaController extends Controller
{
    /**
     * Listar minhas demandas
     *
     * Todas as demandas da OSC, inclusive as concluídas, com o total de contatos de cada uma.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Demanda::STATUS))],
            /** Itens por página (padrão 20). */
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $demandas = $request->user()->demandas()
            ->withCount('manifestacoes')
            ->when($filtros['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest('data_criacao')
            ->paginate($filtros['por_pagina'] ?? 20)
            ->withQueryString();

        return DemandaResource::collection($demandas);
    }

    /**
     * Criar demanda
     *
     * A demanda nasce com status "Aberta". O prazo não pode estar no passado.
     */
    public function store(DemandaRequest $request): JsonResponse
    {
        $demanda = $request->user()->demandas()->create($request->dadosDemanda());

        return DemandaResource::make($demanda->refresh()->loadCount('manifestacoes'))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detalhar demanda
     */
    public function show(Demanda $demanda): DemandaResource
    {
        Gate::authorize('gerenciar', $demanda);

        return DemandaResource::make($demanda->loadCount('manifestacoes'));
    }

    /**
     * Substituir demanda
     *
     * Atualização completa: todos os campos são obrigatórios. `urgencia` ausente significa `false`.
     */
    public function update(DemandaRequest $request, Demanda $demanda): DemandaResource
    {
        return $this->salvar($request, $demanda);
    }

    /**
     * Atualizar parte da demanda
     *
     * Atualização parcial: envie apenas os campos que deseja alterar.
     */
    public function atualizarParcial(DemandaParcialRequest $request, Demanda $demanda): DemandaResource
    {
        return $this->salvar($request, $demanda);
    }

    /**
     * Alterar status
     *
     * "Concluída" retira a demanda da listagem pública (RF07).
     */
    public function status(Request $request, Demanda $demanda): DemandaResource
    {
        Gate::authorize('gerenciar', $demanda);

        $demanda->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Demanda::STATUS))],
        ]));

        return DemandaResource::make($demanda->refresh()->loadCount('manifestacoes'));
    }

    /**
     * Excluir demanda
     *
     * Exclui também as manifestações de interesse recebidas.
     */
    public function destroy(Demanda $demanda): Response
    {
        Gate::authorize('gerenciar', $demanda);

        $demanda->delete();

        return response()->noContent();
    }

    private function salvar(DemandaRequest $request, Demanda $demanda): DemandaResource
    {
        Gate::authorize('gerenciar', $demanda);

        $demanda->update($request->dadosDemanda());

        return DemandaResource::make($demanda->refresh()->loadCount('manifestacoes'));
    }
}
