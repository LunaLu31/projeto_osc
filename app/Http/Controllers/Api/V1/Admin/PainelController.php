<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdmResource;
use App\Services\MetricasRede;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Administração', 'Gestão das OSCs e moderação de todas as demandas da rede. Requer token de administrador.', weight: 3)]
class PainelController extends Controller
{
    /**
     * Métricas globais
     *
     * Total de OSCs, demandas por status e manifestações de interesse (RF09).
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'admin' => AdmResource::make($request->user()),
            'data' => (new MetricasRede)->resumo(),
        ]);
    }
}
