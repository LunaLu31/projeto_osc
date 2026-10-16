<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_metrics_reflect_database_records(): void
    {
        $admin = Adm::create([
            'nome' => 'Administrador',
            'email' => 'admin@example.com',
            'senha_hash' => 'hash',
        ]);

        $osc = Osc::create([
            'id_admin' => $admin->id_admin,
            'nome' => 'Organização Exemplo',
            'email' => 'osc@example.com',
            'senha_hash' => 'hash',
        ]);

        $activeDemand = Demanda::create([
            'id_osc' => $osc->id_osc,
            'titulo' => 'Demanda ativa',
            'status' => 'aberta',
        ]);

        Demanda::create([
            'id_osc' => $osc->id_osc,
            'titulo' => 'Demanda concluída',
            'status' => 'concluída',
        ]);

        Demanda::create([
            'id_osc' => $osc->id_osc,
            'titulo' => 'Demanda cancelada',
            'status' => 'cancelada',
        ]);

        ManifestacaoInteresse::create([
            'id_demanda' => $activeDemand->id_demanda,
            'nome_apoiador' => 'Maria Silva',
            'email' => 'maria@example.com',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('totalOscs', 1)
            ->assertViewHas('activeDemands', 1)
            ->assertViewHas('completedDemands', 1)
            ->assertViewHas('totalInterests', 1)
            ->assertViewHas('monthlyInterests', 1)
            ->assertViewHas('featuredOscInitials', fn($initials) => $initials->all() === ['OE']);
    }
}
