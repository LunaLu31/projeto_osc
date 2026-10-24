<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use App\Models\Osc;
use App\Services\MetricasRede;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Página inicial: métricas da rede e listagem pública de demandas (RF04).
 */
class FeedController extends Controller
{
    private const POR_PAGINA = 12;

    public function __invoke(Request $request): Response
    {
        // Valores inválidos são ignorados pelo scope (a página nunca quebra por filtro manipulado).
        $filtros = [
            'busca' => mb_substr((string) $request->query('busca', ''), 0, 100),
            'categoria' => $request->query('categoria'),
            'status' => $request->query('status'),
            'urgente' => $request->boolean('urgente'),
        ];

        // Urgentes primeiro: o apoiador vê de imediato o que é mais crítico.
        $demandas = Demanda::publicas()
            ->filtrar($filtros)
            ->with('osc:id_osc,nome,area_atuacao,descricao,endereco')
            ->orderByDesc('urgencia')
            ->latest('data_criacao')
            ->paginate(self::POR_PAGINA)
            ->withQueryString()
            ->fragment('demandas');

        return Inertia::render('Publico/Feed', [
            'demandas' => DemandaResource::collection($demandas),
            'filtros' => $filtros,
            'metricas' => (new MetricasRede)->resumo(),
            'iniciaisOscsDestaque' => Osc::orderBy('nome')->limit(3)->get(['id_osc', 'nome'])->map->iniciais,
        ]);
    }
}
