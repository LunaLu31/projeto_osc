<?php

namespace App\Services;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;

/**
 * Indicadores do painel da OSC. Centraliza as consultas agregadas para que
 * a visão geral (e, na versão B, os gráficos) usem a mesma fonte de dados.
 */
class MetricasOsc
{
    public const DIAS_RECENTES = 30;

    public function __construct(private Osc $osc) {}

    /**
     * @return array{
     *     demandas_por_status: array<string, int>,
     *     demandas_ativas: int,
     *     demandas_concluidas: int,
     *     total_contatos: int,
     *     contatos_recentes: int,
     *     contatos_por_tipo: array<string, int>
     * }
     */
    public function resumo(): array
    {
        $porStatus = $this->contarPor($this->osc->demandas()->toBase(), 'status', array_keys(Demanda::STATUS));
        $porTipo = $this->contarPor($this->osc->manifestacoes()->toBase(), 'tipo_interesse', ManifestacaoInteresse::TIPOS);

        return [
            'demandas_por_status' => $porStatus,
            'demandas_ativas' => array_sum(array_intersect_key($porStatus, array_flip(Demanda::STATUS_PUBLICOS))),
            'demandas_concluidas' => $porStatus[Demanda::STATUS_CONCLUIDA],
            'total_contatos' => array_sum($porTipo),
            'contatos_recentes' => $this->osc->manifestacoes()
                ->where('data_manifestacao', '>=', now()->subDays(self::DIAS_RECENTES))
                ->count(),
            'contatos_por_tipo' => $porTipo,
        ];
    }

    /**
     * Contagem agrupada por coluna, com zero para os valores sem registros.
     *
     * @param  list<string>  $valores
     * @return array<string, int>
     */
    private function contarPor($consulta, string $coluna, array $valores): array
    {
        $totais = $consulta->selectRaw("{$coluna} as chave, count(*) as total")
            ->groupBy($coluna)
            ->pluck('total', 'chave');

        return collect($valores)->mapWithKeys(fn (string $valor) => [$valor => (int) ($totais[$valor] ?? 0)])->all();
    }
}
