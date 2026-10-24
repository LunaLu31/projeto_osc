<?php

namespace Database\Factories;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ManifestacaoInteresse>
 */
class ManifestacaoInteresseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_demanda' => Demanda::factory(),
            'nome_apoiador' => fake()->name(),
            'telefone' => null,
            'email' => fake()->unique()->safeEmail(),
            'tipo_interesse' => 'Voluntário',
            'mensagem' => null,
        ];
    }
}
