<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Painel do administrador: métricas globais e gestão da rede (RF09).
 */
class PainelController extends Controller
{
    public function __invoke(Request $request): View
    {
        $oscs = Osc::withCount('demandas')->orderBy('nome')->get();
        $demandas = Demanda::with('osc:id_osc,nome')
            ->withCount('manifestacoes')
            ->latest('data_criacao')
            ->get();

        return view('admin.painel', [
            'admin' => $request->user('admin'),
            'oscs' => $oscs,
            'demandas' => $demandas,
            'demandasPorStatus' => $demandas->countBy('status'),
            'totalManifestacoes' => $demandas->sum('manifestacoes_count'),
        ]);
    }
}
