<?php

namespace App\Http\Resources\V1;

use App\Models\Adm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Adm
 */
class AdmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_admin,
            'nome' => $this->nome,
            'iniciais' => $this->iniciais,
            'email' => $this->email,
        ];
    }
}
