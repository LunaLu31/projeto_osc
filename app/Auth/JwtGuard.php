<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;

/**
 * Guard de login por JWT (driver "jwt" em config/auth.php).
 *
 * - Origem "cookie" (site): o token fica num cookie HttpOnly, fora do alcance do JavaScript.
 *   Os formulários do site seguem protegidos pelo token CSRF da sessão.
 * - Origem "bearer" (API): o token vem no cabeçalho Authorization. Guards separados garantem
 *   que um cookie do site nunca autentique a API (que não tem proteção CSRF).
 */
class JwtGuard implements Guard
{
    use GuardHelpers;

    /** Cookies do site, um por área: OSC e administrador podem estar conectados ao mesmo tempo. */
    public const COOKIES = ['osc' => 'redcam_jwt_osc', 'admin' => 'redcam_jwt_admin'];

    /** Claims do token desta requisição (ou do recém-emitido). */
    private ?object $claims = null;

    /** O token desta requisição já foi lido (evita decodificar duas vezes). */
    private bool $tokenLido = false;

    /** Já emitiu um token nesta requisição (login ou renovação). */
    private bool $emitiuToken = false;

    public function __construct(
        private TokensJwt $tokens,
        UserProvider $provider,
        private Request $request,
        private string $perfil,
        private string $origem,
    ) {
        $this->provider = $provider;
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null || $this->tokenLido) {
            return $this->user;
        }

        $this->tokenLido = true;
        $claims = $this->tokens->ler($this->tokenDaRequisicao(), $this->perfil);
        $usuario = $claims ? $this->provider->retrieveById($claims->sub) : null;

        // Usuário excluído ou senha trocada depois da emissão: o token não vale mais.
        if (! $usuario || ! $this->tokens->pertenceA($claims, $usuario)) {
            return null;
        }

        $this->claims = $claims;

        return $this->user = $usuario;
    }

    public function validate(array $credentials = []): bool
    {
        $usuario = $this->provider->retrieveByCredentials($credentials);

        return $usuario !== null && $this->provider->validateCredentials($usuario, $credentials);
    }

    /**
     * Emite um token para o usuário. No site, ele vai num cookie HttpOnly da resposta:
     * persistente com "Manter conectado", ou de sessão (some ao fechar o navegador).
     */
    public function login(Authenticatable $usuario, bool $lembrar = false): TokenJwt
    {
        $emitido = $this->tokens->emitir($usuario, $this->perfil, $lembrar);

        $this->setUser($usuario);
        $this->claims = $emitido->claims;
        $this->emitiuToken = true;

        if ($this->origem === 'cookie') {
            $minutos = $lembrar ? (int) ceil(Carbon::now()->diffInMinutes($emitido->expiraEm)) : 0;
            Cookie::queue(Cookie::make($this->nomeCookie(), $emitido->token, $minutos));
        }

        return $emitido;
    }

    /** Revoga o token atual (vale também para cópias dele) e, no site, apaga o cookie. */
    public function logout(): void
    {
        if ($this->user() !== null && $this->claims !== null) {
            $this->tokens->revogar($this->claims);
        }

        if ($this->origem === 'cookie') {
            Cookie::queue(Cookie::forget($this->nomeCookie()));
        }

        $this->user = null;
        $this->claims = null;
        $this->tokenLido = true;
    }

    /** API: troca o token atual por um novo e revoga o anterior. */
    public function trocarToken(): ?TokenJwt
    {
        $usuario = $this->user();
        $anterior = $this->claims;

        if ($usuario === null || $anterior === null) {
            return null;
        }

        $novo = $this->login($usuario, (bool) $anterior->lembrar);
        $this->tokens->revogar($anterior);

        return $novo;
    }

    /**
     * Site: renova o cookie de quem está usando o sistema (veja TokensJwt::precisaRenovar).
     * O token anterior não é revogado: requisições paralelas que ainda o usam continuam válidas,
     * e ele expira sozinho antes do novo.
     */
    public function renovarSeNecessario(): void
    {
        if ($this->origem !== 'cookie' || $this->emitiuToken || $this->user() === null || $this->claims === null) {
            return;
        }

        if ($this->tokens->precisaRenovar($this->claims)) {
            $this->login($this->user, (bool) $this->claims->lembrar);
        }
    }

    /** Claims do token atual: lembrar, expiração (exp), jti... */
    public function claims(): ?object
    {
        return $this->user() !== null ? $this->claims : null;
    }

    /**
     * Nova requisição (registrado em AppServiceProvider): o usuário lido do token anterior não vale
     * mais; um usuário definido diretamente (actingAs nos testes) é mantido.
     */
    public function setRequest(Request $request): static
    {
        if ($this->claims !== null || $this->tokenLido) {
            $this->user = null;
        }

        $this->request = $request;
        $this->claims = null;
        $this->tokenLido = false;
        $this->emitiuToken = false;

        return $this;
    }

    private function tokenDaRequisicao(): ?string
    {
        $token = $this->origem === 'cookie'
            ? $this->request->cookie($this->nomeCookie())
            : $this->request->bearerToken();

        return is_string($token) ? $token : null;
    }

    private function nomeCookie(): string
    {
        return self::COOKIES[$this->perfil];
    }
}
