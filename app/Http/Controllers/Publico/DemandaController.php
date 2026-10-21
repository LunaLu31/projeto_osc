<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use App\Rules\EnvioHumano;
use App\Support\Turnstile;
use Inertia\Inertia;
use Inertia\Response;

class DemandaController extends Controller
{
    /**
     * Detalhe público da demanda com o formulário "Quero ajudar" (RF05).
     */
    public function show(Demanda $demanda): Response
    {
        // Demandas concluídas saem da área pública (RF07).
        abort_unless($demanda->estaPublica(), 404);

        return Inertia::render('Publico/Demanda', [
            'demanda' => DemandaResource::make($demanda->load('osc:id_osc,nome,area_atuacao,descricao,endereco'))->resolve(),
            // Proteção anti-spam do formulário (ver ManifestacaoInteresseRequest).
            'antiSpam' => [
                'carregadoEm' => EnvioHumano::marcarAgora(),
                'turnstileSiteKey' => Turnstile::ativo() ? Turnstile::chaveDoSite() : null,
            ],
        ]);
    }
}
