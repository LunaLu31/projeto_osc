<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use Illuminate\View\View;

class DemandaController extends Controller
{
    /**
     * Detalhe público da demanda com o formulário "Quero ajudar" (RF05).
     */
    public function show(Demanda $demanda): View
    {
        // Demandas concluídas saem da área pública (RF07).
        abort_unless($demanda->estaPublica(), 404);

        $demanda->load('osc:id_osc,nome,area_atuacao,descricao,endereco');

        return view('publico.demanda', [
            'demanda' => $demanda,
            'tiposInteresse' => ManifestacaoInteresse::TIPOS,
        ]);
    }
}
