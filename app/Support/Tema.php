<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Tema visual escolhido (claro ou escuro), guardado no cookie `tema`.
 * Lido no servidor para que o HTML (inclusive o do SSR) já chegue no tema certo.
 * Sem escolha salva, vale "sistema": o tema do dispositivo (prefers-color-scheme).
 */
class Tema
{
    public const COOKIE = 'tema';

    public const OPCOES = ['claro', 'escuro'];

    public static function atual(Request $request): string
    {
        $tema = $request->cookie(self::COOKIE);

        return in_array($tema, self::OPCOES, true) ? $tema : 'sistema';
    }
}
