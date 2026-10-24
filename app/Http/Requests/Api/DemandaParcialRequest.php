<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\DemandaRequest;

/**
 * PATCH de demanda: valida e grava somente os campos enviados.
 */
class DemandaParcialRequest extends DemandaRequest
{
    public function rules(): array
    {
        return array_map(fn (array $regras) => ['sometimes', ...$regras], parent::rules());
    }

    /**
     * Campo ausente significa "não alterar" (inclusive `urgencia`).
     */
    public function dadosDemanda(): array
    {
        return $this->validated();
    }
}
