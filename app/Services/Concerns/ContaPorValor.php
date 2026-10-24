<?php

namespace App\Services\Concerns;

use Illuminate\Database\Query\Builder;

trait ContaPorValor
{
    /**
     * Contagem agrupada por coluna, com zero para os valores sem registros.
     *
     * @param  list<string>  $valores
     * @return array<string, int>
     */
    private function contarPor(Builder $consulta, string $coluna, array $valores): array
    {
        $totais = $consulta->selectRaw("{$coluna} as chave, count(*) as total")
            ->groupBy($coluna)
            ->pluck('total', 'chave');

        $contagem = [];

        foreach ($valores as $valor) {
            $contagem[$valor] = (int) ($totais[$valor] ?? 0);
        }

        return $contagem;
    }

    /**
     * Contagem por mês (AAAA-MM) dos últimos `$meses` meses, do mais antigo ao atual, com zero nos meses vazios.
     * Agrupa em PHP, no fuso da aplicação, para não depender das funções de data de cada banco.
     *
     * @return list<array{mes: string, total: int}>
     */
    private function contarPorMes(Builder $consulta, string $coluna, int $meses): array
    {
        $inicio = now()->startOfMonth()->subMonths($meses - 1);

        $totais = $consulta->where($coluna, '>=', $inicio)
            ->pluck($coluna)
            ->countBy(fn (string $data) => substr($data, 0, 7));

        $serie = [];

        for ($i = 0; $i < $meses; $i++) {
            $mes = $inicio->copy()->addMonths($i)->format('Y-m');
            $serie[] = ['mes' => $mes, 'total' => (int) ($totais[$mes] ?? 0)];
        }

        return $serie;
    }
}
