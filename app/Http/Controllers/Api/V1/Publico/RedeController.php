<?php

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Services\MetricasRede;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Público', 'Consulta de demandas e manifestação de interesse, sem login.', weight: 1)]
class RedeController extends Controller
{
    /**
     * Estatísticas da rede
     *
     * Totais exibidos na página inicial: OSCs, demandas por status e manifestações.
     *
     * @unauthenticated
     */
    public function estatisticas(): JsonResponse
    {
        return response()->json(['data' => (new MetricasRede)->resumo()]);
    }

    /**
     * Valores de referência
     *
     * Listas usadas em filtros e formulários: categorias, status e tipos de interesse.
     *
     * @unauthenticated
     */
    public function metadados(): JsonResponse
    {
        return response()->json([
            'data' => [
                'categorias' => Demanda::CATEGORIAS,
                'status' => collect(Demanda::STATUS)
                    ->map(fn (string $rotulo, string $valor) => ['valor' => $valor, 'rotulo' => $rotulo])
                    ->values(),
                'status_publicos' => Demanda::STATUS_PUBLICOS,
                'tipos_interesse' => ManifestacaoInteresse::TIPOS,
            ],
        ]);
    }
}
