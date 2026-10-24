<?php

namespace App\Services;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use App\Services\Concerns\ContaPorValor;

/**
 * Indicadores globais da rede: página inicial, painel do administrador (RF09) e API.
 */
class MetricasRede
{
    use ContaPorValor;

    /** Meses exibidos no gráfico de manifestações da visão geral do administrador. */
    public const MESES_GRAFICO = 6;

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
        /** @var array<string, int> $porStatus */
        $porStatus = $this->contarPor(Demanda::query()->toBase(), 'status', array_keys(Demanda::STATUS));

        return [
            'total_oscs' => Osc::count(),
            'demandas_por_status' => $porStatus,
            'demandas_ativas' => array_sum(array_intersect_key($porStatus, array_flip(Demanda::STATUS_PUBLICOS))),
            'demandas_concluidas' => (int) $porStatus[Demanda::STATUS_CONCLUIDA],
            'total_manifestacoes' => ManifestacaoInteresse::count(),
            'manifestacoes_no_mes' => ManifestacaoInteresse::where('data_manifestacao', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Resumo acrescido dos indicadores da visão geral do administrador.
     *
     * @return array{
     *     total_oscs: int,
     *     demandas_por_status: array<string, int>,
     *     demandas_ativas: int,
     *     demandas_concluidas: int,
     *     total_manifestacoes: int,
     *     manifestacoes_no_mes: int,
     *     demandas_ativas_por_categoria: array<string, int>,
     *     manifestacoes_por_mes: list<array{mes: string, total: int}>
     * }
     */
    public function detalhado(): array
    {
        // O que a rede mais precisa agora: categorias das demandas ativas, da maior para a menor.
        $porCategoria = $this->contarPor(Demanda::publicas()->toBase(), 'categoria', Demanda::CATEGORIAS);
        arsort($porCategoria);

        return [
            ...$this->resumo(),
            'demandas_ativas_por_categoria' => $porCategoria,
            'manifestacoes_por_mes' => $this->contarPorMes(ManifestacaoInteresse::query()->toBase(), 'data_manifestacao', self::MESES_GRAFICO),
        ];
    }
}
