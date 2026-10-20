<?php

namespace Database\Factories;

use App\Models\Adm;
use App\Models\Osc;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Osc>
 */
class OscFactory extends Factory
{
    protected static ?string $senhaHash = null;

    public function definition(): array
    {
        return [
            'id_admin' => Adm::factory(),
            'nome' => fake()->company(),
            'area_atuacao' => 'Educação',
            'descricao' => fake()->sentence(),
            'endereco' => 'Camaçari - BA',
            'telefone' => '(71) 99999-0000',
            'email' => fake()->unique()->safeEmail(),
            'senha_hash' => static::$senhaHash ??= Hash::make('senha-segura'),
        ];
    }
}
