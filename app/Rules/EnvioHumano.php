<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Crypt;

/**
 * Armadilha de tempo do honeypot: robôs costumam enviar o formulário
 * imediatamente após carregá-lo. O valor é o instante de renderização, criptografado.
 */
class EnvioHumano implements ValidationRule
{
    public const SEGUNDOS_MINIMOS = 3;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $carregadoEm = (int) Crypt::decryptString((string) $value);
        } catch (DecryptException) {
            $carregadoEm = null;
        }

        if (! $carregadoEm || now()->getTimestamp() - $carregadoEm < self::SEGUNDOS_MINIMOS) {
            $fail('Não foi possível enviar o formulário. Aguarde alguns segundos e tente novamente.');
        }
    }

    /**
     * Valor do campo oculto, gerado na renderização do formulário.
     */
    public static function marcarAgora(): string
    {
        return Crypt::encryptString((string) now()->getTimestamp());
    }
}
