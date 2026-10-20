<?php

namespace Database\Seeders;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Database\Seeder;

/**
 * Dados fictícios para apresentação: php artisan db:seed --class=DemonstracaoSeeder
 * Todas as OSCs de demonstração usam a senha "senha-segura".
 */
class DemonstracaoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdmSeeder::class);
        $admin = Adm::where('email', 'admin@uoscc.org')->firstOrFail();

        Osc::factory(3)->for($admin, 'adm')->create()->each(function (Osc $osc) {
            Demanda::factory(2)->for($osc)->create();
            Demanda::factory()->for($osc)->create(['urgencia' => true]);
            Demanda::factory()->for($osc)->status(Demanda::STATUS_PARCIAL)->create();

            ManifestacaoInteresse::factory(2)->for($osc->demandas()->first())->create();
        });
    }
}
