<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Moderação: o administrador vê e pode intervir em qualquer demanda (RF07).
 */
#[Group('Administração', 'Gestão das OSCs e moderação de todas as demandas da rede. Requer token de administrador.', weight: 3)]
class DemandaController extends Controller
{
    /**
     * Listar todas as demandas
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Demanda::STATUS))],
            /** ID da OSC. */
            'osc' => ['nullable', 'integer'],
            /** Busca no título da demanda ou no nome da OSC. */
            'busca' => ['nullable', 'string', 'max:100'],
            /** Itens por página (padrão 20). */
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $demandas = Demanda::query()
            ->filtrar(['busca' => $filtros['busca'] ?? null])
            ->when($filtros['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filtros['osc'] ?? null, fn ($q, $osc) => $q->where('id_osc', $osc))
            ->with('osc:id_osc,nome,area_atuacao,descricao,endereco')
            ->withCount('manifestacoes')
            ->latest('data_criacao')
            ->paginate($filtros['por_pagina'] ?? 20)
            ->withQueryString();

        return DemandaResource::collection($demandas);
    }

    /**
     * Detalhar demanda
     */
    public function show(Demanda $demanda): DemandaResource
    {
        return DemandaResource::make($demanda->load('osc:id_osc,nome,area_atuacao,descricao,endereco')->loadCount('manifestacoes'));
    }

    /**
     * Alterar status
     */
    public function status(Request $request, Demanda $demanda): DemandaResource
    {
        $demanda->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Demanda::STATUS))],
        ]));

        return $this->show($demanda->refresh());
    }

    /**
     * Excluir demanda
     */
    public function destroy(Demanda $demanda): Response
    {
        $demanda->delete();

        return response()->noContent();
    }
}
