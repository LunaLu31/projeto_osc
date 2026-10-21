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
}
