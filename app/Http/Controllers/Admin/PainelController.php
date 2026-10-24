<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Http\Resources\V1\OscResource;
use App\Models\Demanda;
use App\Models\Osc;
use App\Services\MetricasRede;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Visão geral do administrador: gráficos da rede e listas que pedem ação (RF09).
 */
class PainelController extends Controller
{
    private const ITENS_POR_LISTA = 5;

    public function __invoke(): Response
    {
        // Demandas ainda no feed público cujo prazo já passou: candidatas à moderação.
        $prazosVencidos = Demanda::publicas()->where('prazo', '<', today()->toDateString());
        // OSCs cadastradas que não têm nada publicado agora: acompanhamento da adesão.
        $oscsSemDemandasAtivas = Osc::whereDoesntHave('demandas', fn (Builder $q) => $q->publicas());

        return Inertia::render('Admin/Painel', [
            'metricas' => (new MetricasRede)->detalhado(),
            'prazosVencidos' => [
                'total' => $prazosVencidos->clone()->count(),
                'itens' => DemandaResource::collection($prazosVencidos
                    ->with('osc:id_osc,nome,area_atuacao,descricao,endereco')
                    ->orderBy('prazo')
                    ->limit(self::ITENS_POR_LISTA)
                    ->get())->resolve(),
            ],
            'oscsSemDemandasAtivas' => [
                'total' => $oscsSemDemandasAtivas->clone()->count(),
                'itens' => OscResource::collection($oscsSemDemandasAtivas
                    ->withCount('demandas')
                    ->orderBy('data_cadastro')
                    ->limit(self::ITENS_POR_LISTA)
                    ->get())->resolve(),
            ],
        ]);
    }
}
