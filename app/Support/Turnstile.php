<?php

namespace App\Support;

/**
 * Cloudflare Turnstile: verificação anti-robô sem quebra-cabeças e sem rastrear
 * o usuário para publicidade (mais adequado à LGPD que o reCAPTCHA).
 * Fica desligado enquanto TURNSTILE_SITE_KEY/TURNSTILE_SECRET_KEY não forem definidas.
 */
class Turnstile
{
    public const ORIGEM = 'https://challenges.cloudflare.com';

    public const VERIFICACAO = self::ORIGEM.'/turnstile/v0/siteverify';

    /** Nome do campo que o widget adiciona ao formulário. */
    public const CAMPO = 'cf-turnstile-response';

    public static function ativo(): bool
    {
        return filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret_key'));
    }

    public static function chaveDoSite(): ?string
    {
        return config('services.turnstile.site_key');
    }
}
