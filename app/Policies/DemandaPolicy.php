<?php

namespace App\Policies;

use App\Models\Demanda;
use App\Models\Osc;

/**
 * Uma OSC só gerencia as próprias demandas (resposta 7 do levantamento).
 * O administrador usa rotas próprias, protegidas pelo guard `admin`.
 */
class DemandaPolicy
{
    public function gerenciar(Osc $osc, Demanda $demanda): bool
    {
        return (int) $demanda->id_osc === (int) $osc->id_osc;
    }
}
