<?php

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

#[Group('Público', 'Consulta de demandas e manifestação de interesse, sem login.', weight: 1)]
class DemandaController extends Controller
{
    /**
     * Listar demandas abertas
     *
     * Demandas com status "Aberta" ou "Parcial", com as urgentes primeiro (RF04).
     * Demandas concluídas não aparecem.
     *
     * @unauthenticated
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->validate([
            /** Busca no título da demanda ou no nome da OSC. */
            'busca' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', Rule::in(Demanda::CATEGORIAS)],
            'status' => ['nullable', Rule::in(Demanda::STATUS_PUBLICOS)],
            /** Use 1 para listar somente as urgentes. */
            'urgente' => ['nullable', 'boolean'],
            /** Itens por página (padrão 12). */
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $demandas = Demanda::publicas()
            ->filtrar(['urgente' => $request->boolean('urgente')] + $filtros)
            ->with('osc:id_osc,nome,area_atuacao,descricao,endereco')
            ->orderByDesc('urgencia')
            ->latest('data_criacao')
            ->paginate($filtros['por_pagina'] ?? 12)
            ->withQueryString();

        return DemandaResource::collection($demandas);
    }

    /**
     * Detalhar demanda
     *
     * Retorna 404 para demandas concluídas, que saem da área pública (RF07).
     *
     * @unauthenticated
     */
    public function show(Demanda $demanda): DemandaResource
    {
        abort_unless($demanda->estaPublica(), 404);

        return DemandaResource::make($demanda->load('osc:id_osc,nome,area_atuacao,descricao,endereco'));
    }
}
