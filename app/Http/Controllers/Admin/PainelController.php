<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Http\Resources\V1\OscResource;
use App\Models\Demanda;
use App\Models\Osc;
use App\Services\MetricasRede;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Painel do administrador: métricas globais e gestão da rede (RF09).
 */
class PainelController extends Controller
{
    private const OSCS_POR_PAGINA = 10;

    private const DEMANDAS_POR_PAGINA = 15;

    public function __invoke(): Response
    {
        return Inertia::render('Admin/Painel', [
            'metricas' => (new MetricasRede)->resumo(),
            // Listas paginadas no servidor, cada uma com seu parâmetro de página.
            'oscs' => OscResource::collection(Osc::withCount('demandas')
                ->orderBy('nome')
                ->paginate(self::OSCS_POR_PAGINA, pageName: 'pagina_oscs')
                ->withQueryString()),
            'demandas' => DemandaResource::collection(Demanda::with('osc:id_osc,nome,area_atuacao,descricao,endereco')
                ->withCount('manifestacoes')
                ->latest('data_criacao')
                ->paginate(self::DEMANDAS_POR_PAGINA, pageName: 'pagina_demandas')
                ->withQueryString()),
        ]);
    }
}
