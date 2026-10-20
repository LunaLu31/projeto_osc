<?php

namespace App\Services;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;

/**
 * Indicadores globais da rede: página inicial, painel do administrador (RF09) e API.
 */
class MetricasRede
{
    /**
     * @return array{
     *     total_oscs: int,
     *     demandas_por_status: array<string, int>,
     *     demandas_ativas: int,
     *     demandas_concluidas: int,
     *     total_manifestacoes: int,
     *     manifestacoes_no_mes: int
     * }
     */
    public function resumo(): array
    {
        $totais = Demanda::query()->toBase()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $porStatus = collect(array_keys(Demanda::STATUS))
            ->mapWithKeys(fn (string $status) => [$status => (int) ($totais[$status] ?? 0)])
            ->all();

        return [
            'total_oscs' => Osc::count(),
            'demandas_por_status' => $porStatus,
            'demandas_ativas' => array_sum(array_intersect_key($porStatus, array_flip(Demanda::STATUS_PUBLICOS))),
            'demandas_concluidas' => $porStatus[Demanda::STATUS_CONCLUIDA],
            'total_manifestacoes' => ManifestacaoInteresse::count(),
            'manifestacoes_no_mes' => ManifestacaoInteresse::where('data_manifestacao', '>=', now()->startOfMonth())->count(),
        ];
    }
}
