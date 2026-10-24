<?php

namespace App\Rules;

use App\Support\Turnstile;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Confere com a Cloudflare o token gerado pelo widget Turnstile.
 */
class TurnstileValido implements ValidationRule
{
    public function __construct(private ?string $ip = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail('Confirme a verificação de segurança antes de enviar.');

            return;
        }

        try {
            $resposta = Http::asForm()->timeout(5)->post(Turnstile::VERIFICACAO, [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => $this->ip,
            ]);
        } catch (ConnectionException $e) {
            Log::warning('Turnstile indisponível', ['erro' => $e->getMessage()]);
            $fail('Não foi possível concluir a verificação de segurança. Tente novamente em instantes.');

            return;
        }

        if (! $resposta->json('success')) {
            $fail('A verificação de segurança falhou. Atualize a página e tente novamente.');
        }
    }
}
