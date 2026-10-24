<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login por e-mail e senha de OSCs e administradores (RF01), com bloqueio
 * temporário após tentativas seguidas para o mesmo e-mail e IP.
 * Usado pelo site (JWT em cookie) e pela API (JWT no cabeçalho Authorization).
 */
class LoginRequest extends FormRequest
{
    private const MAX_TENTATIVAS = 5;

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            /** "Manter conectado" (somente site): o login vale por 7 dias mesmo fechando o navegador. */
            'lembrar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Login do site: valida as credenciais e emite o JWT do guard (cookie HttpOnly).
     *
     * @throws ValidationException
     */
    public function autenticar(string $guard): void
    {
        Auth::guard($guard)->login($this->validarCredenciais($guard), $this->boolean('lembrar'));

        // A sessão continua guardando o token CSRF e as mensagens; um novo ID evita fixação de sessão.
        $this->session()->regenerate();
    }

    /**
     * Confere e-mail e senha no provedor do guard, sem emitir token.
     *
     * @throws ValidationException
     */
    public function validarCredenciais(string $guard): Authenticatable
    {
        $chave = $this->chaveLimite($guard);

        if (RateLimiter::tooManyAttempts($chave, self::MAX_TENTATIVAS)) {
            throw ValidationException::withMessages([
                'email' => 'Muitas tentativas. Tente novamente em '.RateLimiter::availableIn($chave).' segundos.',
            ]);
        }

        $provedor = Auth::guard($guard)->getProvider();
        $credenciais = $this->only('email', 'password');
        $usuario = $provedor->retrieveByCredentials($credenciais);

        if (! $usuario || ! $provedor->validateCredentials($usuario, $credenciais)) {
            RateLimiter::hit($chave);

            throw ValidationException::withMessages(['email' => 'E-mail ou senha inválidos.']);
        }

        RateLimiter::clear($chave);
        $provedor->rehashPasswordIfRequired($usuario, $credenciais);

        return $usuario;
    }

    private function chaveLimite(string $guard): string
    {
        return $guard.'|'.Str::transliterate(Str::lower($this->string('email'))).'|'.$this->ip();
    }
}
