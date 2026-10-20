<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\ManifestacaoInteresseRequest;
use App\Rules\TurnstileValido;
use App\Support\Turnstile;

/**
 * Manifestação de interesse pela API. Clientes da API não renderizam o formulário,
 * então o honeypot e a armadilha de tempo não se aplicam; ficam o limite de
 * requisições por IP e, quando configurado, o token do Cloudflare Turnstile.
 */
class ManifestacaoInteresseApiRequest extends ManifestacaoInteresseRequest
{
    protected function regrasAntiSpam(): array
    {
        if (! Turnstile::ativo()) {
            return [];
        }

        return [
            /** Token gerado pelo widget Cloudflare Turnstile. Obrigatório quando o Turnstile está ativo no servidor. */
            Turnstile::CAMPO => ['required', new TurnstileValido($this->ip())],
        ];
    }
}
