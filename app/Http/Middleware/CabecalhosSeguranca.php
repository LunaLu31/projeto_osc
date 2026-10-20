<?php

namespace App\Http\Middleware;

use App\Support\Turnstile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeçalhos de segurança aplicados a todas as respostas:
 * CSP com nonce, anti-clickjacking, nosniff, política de referência e HSTS.
 */
class CabecalhosSeguranca
{
    public function handle(Request $request, Closure $next): Response
    {
        // Gera o nonce antes da renderização: @vite e scripts/estilos marcados o recebem.
        Vite::useCspNonce();

        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        if (config('seguranca.csp.ativa')) {
            $nome = config('seguranca.csp.somente_relatorio')
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';
            $headers[$nome] = $this->politica($request);
        }

        if ($request->isSecure() && app()->isProduction()) {
            $headers['Strict-Transport-Security'] = 'max-age='.config('seguranca.hsts_max_age').'; includeSubDomains';
        }

        foreach ($headers as $nome => $valor) {
            $response->headers->set($nome, $valor);
        }

        return $response;
    }

    private function politica(Request $request): string
    {
        $nonce = "'nonce-".Vite::cspNonce()."'";
        $turnstile = Turnstile::ativo() ? Turnstile::ORIGEM : null;
        [$viteHttp, $viteWs] = $this->origensViteDev();

        $diretivas = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", $nonce, $turnstile, $viteHttp],
            // Em desenvolvimento o Vite injeta <style> via JS; com nonce o navegador ignoraria 'unsafe-inline'.
            'style-src' => $viteHttp ? ["'self'", "'unsafe-inline'", $viteHttp] : ["'self'", $nonce],
            'img-src' => ["'self'", 'data:'],
            'font-src' => ["'self'", $viteHttp],
            'connect-src' => ["'self'", $turnstile, $viteHttp, $viteWs],
            'frame-src' => [$turnstile ?? "'none'"],
            'frame-ancestors' => ["'none'"],
            'form-action' => ["'self'"],
            'base-uri' => ["'self'"],
            'object-src' => ["'none'"],
        ];

        $politica = collect($diretivas)
            ->map(fn (array $fontes, string $diretiva) => $diretiva.' '.implode(' ', array_filter($fontes)))
            ->implode('; ');

        if ($request->isSecure()) {
            $politica .= '; upgrade-insecure-requests';
        }

        return $politica;
    }

    /**
     * Origens do servidor do Vite quando `npm run dev` está ativo (arquivo public/hot).
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function origensViteDev(): array
    {
        if (! Vite::isRunningHot()) {
            return [null, null];
        }

        $origem = rtrim(trim((string) file_get_contents(Vite::hotFile())), '/');

        // A CSP não aceita IPv6 literal (ex.: http://[::1]:5173). Só em desenvolvimento,
        // libera pelo esquema; o vite.config.js já fixa 127.0.0.1 para evitar este caso.
        if (str_contains($origem, '[')) {
            return [parse_url($origem, PHP_URL_SCHEME).':', 'ws:'];
        }

        return [$origem, preg_replace('/^http/', 'ws', $origem)];
    }
}
