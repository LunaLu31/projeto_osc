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
 * Usado pelo site (sessão) e pela API (token Bearer).
 */
class LoginRequest extends FormRequest
{
    private const MAX_TENTATIVAS = 5;

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            /** Nome do dispositivo/cliente que receberá o token (somente API). */
            'dispositivo' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Login do site: valida as credenciais e inicia a sessão do guard.
     *
     * @throws ValidationException
     */
    public function autenticar(string $guard): void
    {
        Auth::guard($guard)->login($this->validarCredenciais($guard));
        $this->session()->regenerate();
    }

    /**
     * Confere e-mail e senha no provedor do guard, sem criar sessão (usado pela API).
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
