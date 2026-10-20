<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Página inicial: métricas da rede e listagem pública de demandas (RF04).
 */
class FeedController extends Controller
{
    private const POR_PAGINA = 12;

    public function __invoke(Request $request): View
    {
        $filtros = [
            'busca' => mb_substr((string) $request->query('busca', ''), 0, 100),
            'categoria' => $request->query('categoria'),
            'status' => $request->query('status'),
            'urgente' => $request->boolean('urgente'),
        ];

        // Urgentes primeiro: o apoiador vê de imediato o que é mais crítico.
        $demandas = Demanda::publicas()
            ->filtrar($filtros)
            ->with('osc:id_osc,nome,endereco')
            ->orderByDesc('urgencia')
            ->latest('data_criacao')
            ->paginate(self::POR_PAGINA)
            ->withQueryString()
            ->fragment('demandas');

        return view('publico.feed', [
            'demandas' => $demandas,
            'filtros' => $filtros,
            'totalOscs' => Osc::count(),
            'demandasAtivas' => Demanda::publicas()->count(),
            'demandasConcluidas' => Demanda::where('status', Demanda::STATUS_CONCLUIDA)->count(),
            'totalManifestacoes' => ManifestacaoInteresse::count(),
            'manifestacoesNoMes' => ManifestacaoInteresse::where('data_manifestacao', '>=', now()->startOfMonth())->count(),
            'iniciaisOscsDestaque' => Osc::orderBy('nome')->limit(3)->get(['id_osc', 'nome'])->map->iniciais,
        ]);
    }
}
