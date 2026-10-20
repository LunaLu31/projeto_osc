<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Painel da OSC: suas demandas e os contatos de quem manifestou interesse (RF08).
 */
class PainelController extends Controller
{
    public function __invoke(Request $request): View
    {
        $osc = $request->user('osc');
        $demandas = $osc->demandas()
            ->with(['manifestacoes' => fn ($query) => $query->latest('data_manifestacao')])
            ->latest('data_criacao')
            ->get();

        return view('osc.painel', [
            'osc' => $osc,
            'demandas' => $demandas,
            'totalAtivas' => $demandas->filter->estaPublica()->count(),
            'totalConcluidas' => $demandas->where('status', Demanda::STATUS_CONCLUIDA)->count(),
            'totalInteressados' => $demandas->sum(fn (Demanda $demanda) => $demanda->manifestacoes->count()),
        ]);
    }
}
