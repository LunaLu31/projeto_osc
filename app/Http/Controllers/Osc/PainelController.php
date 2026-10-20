<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Services\MetricasOsc;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Visão geral do painel da OSC: indicadores, contatos recentes e prazos próximos.
 */
class PainelController extends Controller
{
    private const DIAS_PRAZO_PROXIMO = 14;

    public function __invoke(Request $request): View
    {
        $osc = $request->user('osc');

        return view('osc.painel', [
            'osc' => $osc,
            'metricas' => (new MetricasOsc($osc))->resumo(),
            'contatosRecentes' => $osc->manifestacoes()
                ->with('demanda:id_demanda,titulo')
                ->latest('data_manifestacao')
                ->limit(5)
                ->get(),
            'prazosProximos' => $osc->demandas()
                ->publicas()
                ->whereBetween('prazo', [today(), today()->addDays(self::DIAS_PRAZO_PROXIMO)])
                ->orderBy('prazo')
                ->get(),
            'diasPrazoProximo' => self::DIAS_PRAZO_PROXIMO,
        ]);
    }
}
