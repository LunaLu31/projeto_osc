<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\OscRequest;

/**
 * PATCH de OSC: valida e grava somente os campos enviados.
 */
class OscParcialRequest extends OscRequest
{
    public function rules(): array
    {
        return array_map(fn (array $regras) => ['sometimes', ...$regras], parent::rules());
    }
}
