<?php

namespace Database\Factories;

use App\Models\Adm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Adm>
 */
class AdmFactory extends Factory
{
    protected static ?string $senhaHash = null;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'senha_hash' => static::$senhaHash ??= Hash::make('senha-segura'),
        ];
    }
}
