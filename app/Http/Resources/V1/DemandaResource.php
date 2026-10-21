<?php

namespace App\Http\Resources\V1;

use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Demanda publicada por uma OSC. Não expõe contatos de apoiadores (RNF06);
 * apenas a contagem, quando solicitada por um painel privado.
 * A OSC aninhada é resolvida aqui para ter o mesmo formato na API e nas props
 * do Inertia (sem um "data" extra).
 *
 * @mixin Demanda
 */
class DemandaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_demanda,
            'titulo' => $this->titulo,
            'categoria' => $this->categoria,
            'descricao' => $this->descricao,
            'quantidade' => $this->quantidade,
            /** Data limite no formato AAAA-MM-DD. */
            'prazo' => $this->prazo?->format('Y-m-d'),
            /** @var 'Aberta'|'Parcial'|'Concluída' */
            'status' => $this->status,
            'status_rotulo' => $this->rotuloStatus(),
            'urgente' => $this->urgencia,
            'criada_em' => $this->data_criacao,
            'atualizada_em' => $this->data_atualizacao,
            /**
             * Organização que publicou a demanda.
             *
             * @var OscPublicaResource
             */
            'osc' => $this->whenLoaded('osc', fn () => OscPublicaResource::make($this->osc)->resolve($request)),
            'total_contatos' => $this->whenCounted('manifestacoes'),
        ];
    }
}
