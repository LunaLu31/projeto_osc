<?php

namespace App\Auth;

use DomainException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Emissão e validação dos JWT (HS256) de login de OSCs e administradores, no site e na API.
 *
 * Revogação sem tabela de tokens:
 * - sair: o `jti` do token entra numa lista de revogados no cache até a data em que ele expiraria;
 * - troca de senha: o claim `sv` é uma impressão (HMAC) do hash da senha. Mudou a senha,
 *   todos os tokens emitidos antes deixam de valer, em todos os dispositivos.
 */
class TokensJwt
{
    private const ALGORITMO = 'HS256';

    /** Renova quando mais de 10% da validade já passou: quem usa o sistema não chega a expirar. */
    private const RENOVAR_APOS = 0.1;

    public function emitir(Authenticatable $usuario, string $perfil, bool $lembrar): TokenJwt
    {
        $agora = Carbon::now();
        $expiraEm = $agora->copy()->addMinutes(
            $lembrar ? config('jwt.validade_minutos') : config('jwt.validade_sem_lembrar_minutos')
        );

        $claims = [
            'iss' => config('app.url'),
            'sub' => (string) $usuario->getAuthIdentifier(),
            'perfil' => $perfil,
            'lembrar' => $lembrar,
            'sv' => $this->impressaoDaSenha($usuario),
            'jti' => (string) Str::uuid(),
            'iat' => $agora->timestamp,
            'nbf' => $agora->timestamp,
            'exp' => $expiraEm->timestamp,
        ];

        return new TokenJwt(JWT::encode($claims, $this->chave(), self::ALGORITMO), (object) $claims, $expiraEm);
    }

    /**
     * Claims de um token com assinatura válida, dentro da validade, não revogado e, se informado,
     * do perfil esperado. Qualquer outro caso (adulterado, expirado, malformado) devolve null.
     */
    public function ler(?string $token, ?string $perfil = null): ?object
    {
        if (! $token) {
            return null;
        }

        try {
            // Segue o relógio do Laravel (inclusive o travel() dos testes).
            JWT::$timestamp = Carbon::now()->timestamp;
            $claims = JWT::decode($token, new Key($this->chave(), self::ALGORITMO));
        } catch (UnexpectedValueException|DomainException|InvalidArgumentException) {
            return null;
        } finally {
            JWT::$timestamp = null;
        }

        if (($claims->iss ?? null) !== config('app.url') || ! isset($claims->sub, $claims->perfil, $claims->jti, $claims->sv)) {
            return null;
        }

        if ($perfil !== null && $claims->perfil !== $perfil) {
            return null;
        }

        return Cache::has($this->chaveRevogado($claims->jti)) ? null : $claims;
    }

    /** O token foi emitido com a senha atual do usuário? */
    public function pertenceA(object $claims, Authenticatable $usuario): bool
    {
        return hash_equals($this->impressaoDaSenha($usuario), (string) $claims->sv);
    }

    public function revogar(object $claims): void
    {
        Cache::put($this->chaveRevogado($claims->jti), true, Carbon::createFromTimestamp($claims->exp));
    }

    public function precisaRenovar(object $claims): bool
    {
        return Carbon::now()->timestamp - $claims->iat > ($claims->exp - $claims->iat) * self::RENOVAR_APOS;
    }

    private function impressaoDaSenha(Authenticatable $usuario): string
    {
        return substr(hash_hmac('sha256', $usuario->getAuthPassword(), $this->chave()), 0, 32);
    }

    private function chave(): string
    {
        $segredo = config('jwt.segredo');

        if (blank($segredo)) {
            $appKey = (string) config('app.key');
            $segredo = $appKey !== '' ? hash_hmac('sha256', 'jwt', $appKey, true) : '';
        }

        // Uma chave curta tornaria a assinatura HS256 quebrável por força bruta.
        if (strlen($segredo) < 32) {
            throw new RuntimeException('Defina JWT_SECRET com pelo menos 32 caracteres (ou a APP_KEY).');
        }

        return $segredo;
    }

    private function chaveRevogado(string $jti): string
    {
        return "jwt:revogado:{$jti}";
    }
}
