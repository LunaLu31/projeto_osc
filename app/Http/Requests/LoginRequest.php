<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login por e-mail e senha de OSCs e administradores (RF01), com bloqueio
 * temporário após tentativas seguidas para o mesmo e-mail e IP.
 */
class LoginRequest extends FormRequest
{
    private const MAX_TENTATIVAS = 5;

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function autenticar(string $guard): void
    {
        $chave = $this->chaveLimite($guard);

        if (RateLimiter::tooManyAttempts($chave, self::MAX_TENTATIVAS)) {
            throw ValidationException::withMessages([
                'email' => 'Muitas tentativas. Tente novamente em '.RateLimiter::availableIn($chave).' segundos.',
            ]);
        }

        if (! Auth::guard($guard)->attempt($this->only('email', 'password'))) {
            RateLimiter::hit($chave);

            throw ValidationException::withMessages(['email' => 'E-mail ou senha inválidos.']);
        }

        RateLimiter::clear($chave);
        $this->session()->regenerate();
    }

    private function chaveLimite(string $guard): string
    {
        return $guard.'|'.Str::transliterate(Str::lower($this->string('email'))).'|'.$this->ip();
    }
}
