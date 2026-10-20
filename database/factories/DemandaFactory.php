<?php

namespace Database\Factories;

use App\Models\Demanda;
use App\Models\Osc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Demanda>
 */
class DemandaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_osc' => Osc::factory(),
            'titulo' => fake()->sentence(3),
            'categoria' => 'Alimentos',
            'descricao' => fake()->paragraph(),
            'quantidade' => 10,
            'prazo' => now()->addMonth()->toDateString(),
            'status' => Demanda::STATUS_ABERTA,
            'urgencia' => false,
        ];
    }

    public function status(string $status): static
    {
        return $this->state(['status' => $status]);
    }
}
