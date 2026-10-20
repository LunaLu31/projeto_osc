<?php

namespace App\Http\Resources\V1;

use App\Models\ManifestacaoInteresse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Manifestação de interesse com os dados de contato do apoiador.
 * Só é retornada à OSC dona da demanda (RNF06 – LGPD).
 *
 * @mixin ManifestacaoInteresse
 */
class ContatoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_manifestacao,
            'nome' => $this->nome_apoiador,
            'telefone' => $this->telefone,
            'email' => $this->email,
            /** @var 'Apoiador/Doação'|'Voluntário'|'Aluno' */
            'tipo_interesse' => $this->tipo_interesse,
            'mensagem' => $this->mensagem,
            'recebido_em' => $this->data_manifestacao,
            'demanda' => $this->whenLoaded('demanda', fn () => [
                'id' => $this->demanda->id_demanda,
                'titulo' => $this->demanda->titulo,
            ]),
        ];
    }
}
