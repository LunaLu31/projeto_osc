<?php

namespace App\Http\Resources\V1;

use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Dados públicos da OSC exibidos junto às demandas. Nunca inclui e-mail de acesso.
 *
 * @mixin Osc
 */
class OscPublicaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_osc,
            'nome' => $this->nome,
            'iniciais' => $this->iniciais,
            'area_atuacao' => $this->area_atuacao,
            'descricao' => $this->descricao,
            'endereco' => $this->endereco,
        ];
    }
}
