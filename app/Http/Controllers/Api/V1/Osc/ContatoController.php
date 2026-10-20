<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ContatoResource;
use App\Models\ManifestacaoInteresse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

#[Group('OSC', 'Área da organização: demandas próprias e contatos recebidos. Requer token de OSC.', weight: 2)]
class ContatoController extends Controller
{
    /**
     * Listar contatos recebidos
     *
     * Apoiadores que manifestaram interesse nas demandas da OSC (RF08), dos mais recentes aos mais antigos.
     * Dados pessoais protegidos pela LGPD: use-os apenas para responder sobre a demanda indicada (RNF06).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $osc = $request->user();

        $filtros = $request->validate([
            /** ID de uma demanda da própria OSC. */
            'demanda' => ['nullable', 'integer'],
            'tipo' => ['nullable', Rule::in(ManifestacaoInteresse::TIPOS)],
            /** Itens por página (padrão 20). */
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        // Nunca filtra por demanda de outra OSC (evita enumerar demandas alheias).
        if (isset($filtros['demanda']) && ! $osc->demandas()->whereKey($filtros['demanda'])->exists()) {
            throw ValidationException::withMessages(['demanda' => 'Demanda não encontrada entre as suas demandas.']);
        }

        $contatos = $osc->manifestacoes()
            ->with('demanda:id_demanda,titulo')
            ->filtrar(isset($filtros['demanda']) ? (int) $filtros['demanda'] : null, $filtros['tipo'] ?? null)
            ->latest('data_manifestacao')
            ->paginate($filtros['por_pagina'] ?? 20)
            ->withQueryString();

        return ContatoResource::collection($contatos);
    }
}
