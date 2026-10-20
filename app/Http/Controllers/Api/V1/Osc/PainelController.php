<?php

namespace App\Http\Controllers\Api\V1\Osc;

use App\Http\Controllers\Controller;
use App\Services\MetricasOsc;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('OSC', 'Área da organização: demandas próprias e contatos recebidos. Requer token de OSC.', weight: 2)]
class PainelController extends Controller
{
    /**
     * Indicadores do painel
     *
     * Demandas por status e contatos recebidos por tipo de interesse (base dos gráficos da versão B).
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(['data' => (new MetricasOsc($request->user()))->resumo()]);
    }
}
