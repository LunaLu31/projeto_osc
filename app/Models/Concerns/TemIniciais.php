<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Iniciais do nome usadas nos avatares (ex.: "Organização Exemplo" => "OE").
 */
trait TemIniciais
{
    protected function iniciais(): Attribute
    {
        return Attribute::get(fn () => collect(explode(' ', trim((string) $this->nome)))
            ->filter()
            ->take(2)
            ->map(fn (string $parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
            ->implode(''));
    }
}
