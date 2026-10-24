<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ContatoResource;
use App\Http\Resources\V1\DemandaResource;
use App\Services\MetricasOsc;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Visão geral do painel da OSC: indicadores, contatos recentes e prazos próximos.
 */
class PainelController extends Controller
{
    private const DIAS_PRAZO_PROXIMO = 14;

    public function __invoke(Request $request): Response
    {
        $osc = $request->user('osc');

        return Inertia::render('Osc/Painel', [
            'metricas' => (new MetricasOsc($osc))->resumo(),
            'diasRecentes' => MetricasOsc::DIAS_RECENTES,
            'contatosRecentes' => ContatoResource::collection($osc->manifestacoes()
                ->with('demanda:id_demanda,titulo')
                ->latest('data_manifestacao')
                ->limit(5)
                ->get())->resolve(),
            'prazosProximos' => DemandaResource::collection($osc->demandas()
                ->publicas()
                ->whereBetween('prazo', [today(), today()->addDays(self::DIAS_PRAZO_PROXIMO)])
                ->orderBy('prazo')
                ->get())->resolve(),
            'diasPrazoProximo' => self::DIAS_PRAZO_PROXIMO,
        ]);
    }
}
