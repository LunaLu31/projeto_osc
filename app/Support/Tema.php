<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Preferência de tema visual (claro, escuro ou do sistema), guardada no cookie `tema`.
 * Lida no servidor para que o HTML (inclusive o do SSR) já chegue no tema certo.
 */
class Tema
{
    public const COOKIE = 'tema';

    public const OPCOES = ['sistema', 'claro', 'escuro'];

    public static function atual(Request $request): string
    {
        $tema = $request->cookie(self::COOKIE);

        return in_array($tema, self::OPCOES, true) ? $tema : 'sistema';
    }
}
