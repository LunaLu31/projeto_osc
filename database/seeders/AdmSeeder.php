<?php

namespace Database\Seeders;

use App\Models\Adm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Administrador padrão para desenvolvimento (mesmo do script físico).
 * Troque a senha antes de usar em produção.
 */
class AdmSeeder extends Seeder
{
    public function run(): void
    {
        Adm::firstOrCreate(
            ['email' => 'admin@uoscc.org'],
            ['nome' => 'Administrador CONECTA_OSC', 'senha_hash' => Hash::make('admin123')],
        );
    }
}
