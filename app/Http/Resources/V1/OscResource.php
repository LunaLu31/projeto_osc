<?php

namespace App\Http\Resources\V1;

use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Cadastro completo da OSC: visto pela própria OSC e pelo administrador.
 *
 * @mixin Osc
 */
class OscResource extends JsonResource
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
            'telefone' => $this->telefone,
            'email' => $this->email,
            'cadastrada_em' => $this->data_cadastro,
            'total_demandas' => $this->whenCounted('demandas'),
        ];
    }
}
