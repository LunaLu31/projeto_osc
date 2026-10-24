<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use App\Services\MetricasRede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_without_admins_the_portal_leads_to_the_initial_setup(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertRedirect('/admin/configuracao');
        $this->get('/admin/configuracao')->assertOk()->assertInertia(fn (Assert $pagina) => $pagina->component('Admin/Configuracao'));
    }

    public function test_the_first_admin_can_create_an_account_and_enter_the_portal(): void
    {
        $this->post('/admin/configuracao', [
            'nome' => 'Admin Inicial',
            'email' => 'admin@example.com',
            'password' => 'senhaSegura123',
            'password_confirmation' => 'senhaSegura123',
        ])->assertRedirect('/admin');

        $this->assertDatabaseHas('adms', ['email' => 'admin@example.com']);
        $this->assertAuthenticated('admin');
    }

    public function test_setup_is_blocked_once_an_admin_exists(): void
    {
        Adm::factory()->create();

        $this->get('/admin/configuracao')->assertRedirect('/admin/login');
        $this->post('/admin/configuracao', [
            'nome' => 'Intruso',
            'email' => 'intruso@example.com',
            'password' => 'senhaSegura123',
            'password_confirmation' => 'senhaSegura123',
        ])->assertRedirect('/admin/login');

        $this->assertDatabaseMissing('adms', ['email' => 'intruso@example.com']);
    }

    public function test_an_admin_can_log_in_with_registered_credentials(): void
    {
        $admin = Adm::factory()->create();

        $this->get('/admin/login')->assertOk()->assertInertia(fn (Assert $pagina) => $pagina->component('Admin/Login'));
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'senha-segura'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_an_admin_cannot_log_in_with_an_invalid_password(): void
    {
        $admin = Adm::factory()->create();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'errada'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_an_osc_cannot_use_the_admin_login(): void
    {
        Adm::factory()->create();
        $osc = Osc::factory()->create();

        $this->post('/admin/login', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_overview_shows_the_network_charts(): void
    {
        $admin = Adm::factory()->create();
        $osc = Osc::factory()->create();
        Demanda::factory()->for($osc)->create(['categoria' => 'Alimentos']);
        Demanda::factory()->for($osc)->create(['categoria' => 'Alimentos']);
        Demanda::factory()->for($osc)->create(['categoria' => 'Higiene']);
        Demanda::factory()->for($osc)->status(Demanda::STATUS_CONCLUIDA)->create(['categoria' => 'Saúde']);
        ManifestacaoInteresse::factory()->for(Demanda::first())->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Admin/Painel')
                ->where('auth.admin.id', $admin->id_admin)
                ->where('metricas.demandas_ativas', 3)
                ->where('metricas.demandas_por_status.Concluída', 1)
                ->has('metricas.manifestacoes_por_mes', MetricasRede::MESES_GRAFICO)
                ->where('metricas.manifestacoes_por_mes.'.(MetricasRede::MESES_GRAFICO - 1).'.total', 1));

        // Só demandas ativas, da categoria mais pedida para a menos pedida.
        $categorias = $this->get('/admin')->viewData('page')['props']['metricas']['demandas_ativas_por_categoria'];
        $this->assertSame(['Alimentos' => 2, 'Higiene' => 1], array_slice($categorias, 0, 2, true));
        $this->assertSame(0, $categorias['Saúde']);
    }

    public function test_overview_lists_what_needs_the_admin_attention(): void
    {
        $admin = Adm::factory()->create();
        $ativa = Osc::factory()->create(['nome' => 'OSC ativa']);
        $parada = Osc::factory()->create(['nome' => 'OSC parada']);
        Demanda::factory()->for($ativa)->create(['titulo' => 'Prazo vencido', 'prazo' => today()->subDay()]);
        Demanda::factory()->for($ativa)->create(['prazo' => today()->addWeek()]);
        Demanda::factory()->for($parada)->status(Demanda::STATUS_CONCLUIDA)->create(['prazo' => today()->subMonth()]);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertInertia(fn (Assert $pagina) => $pagina
                // A demanda concluída com prazo passado já saiu do feed: não pede moderação.
                ->where('prazosVencidos.total', 1)
                ->where('prazosVencidos.itens.0.titulo', 'Prazo vencido')
                ->where('prazosVencidos.itens.0.osc.nome', 'OSC ativa')
                ->where('oscsSemDemandasAtivas.total', 1)
                ->where('oscsSemDemandasAtivas.itens.0.nome', 'OSC parada')
                ->where('oscsSemDemandasAtivas.itens.0.total_demandas', 1));
    }

    public function test_network_tab_lists_every_osc_of_the_network(): void
    {
        $admin = Adm::factory()->create();
        $ownedOsc = Osc::factory()->for($admin, 'adm')->create(['nome' => 'OSC cadastrada por mim']);
        $otherOsc = Osc::factory()->create(['nome' => 'OSC de outro admin']);
        Demanda::factory()->for($ownedOsc)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/oscs')
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Admin/Rede')
                ->has('oscs.data', 2)
                ->where('oscs.meta.total', 2));

        $resposta = $this->get('/admin/oscs');
        $this->assertEqualsCanonicalizing([$ownedOsc->nome, $otherOsc->nome], $this->coluna($resposta, 'oscs.data', 'nome'));
        $this->assertEqualsCanonicalizing([1, 0], $this->coluna($resposta, 'oscs.data', 'total_demandas'));
    }

    public function test_network_tab_paginates_the_oscs(): void
    {
        $admin = Adm::factory()->create();
        Osc::factory(12)->for($admin, 'adm')->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/oscs?pagina_oscs=2')
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->has('oscs.data', 2)
                ->where('oscs.meta.current_page', 2)
                ->where('oscs.meta.total', 12));
    }

    public function test_registration_tab_shows_an_empty_osc_form(): void
    {
        $this->actingAs(Adm::factory()->create(), 'admin')
            ->get('/admin/oscs/criar')
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Admin/OscForm')
                ->where('osc', null));
    }

    public function test_moderation_tab_lists_every_demand_of_the_network_paginated(): void
    {
        $admin = Adm::factory()->create();
        Demanda::factory()->for(Osc::factory())->create(['titulo' => 'Demanda da primeira OSC']);
        Demanda::factory()->for(Osc::factory())->create(['titulo' => 'Demanda da segunda OSC']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/demandas')
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Admin/Moderacao')
                ->has('demandas.data', 2)
                ->where('demandas.meta.total', 2));

        $resposta = $this->get('/admin/demandas');
        $this->assertEqualsCanonicalizing(['Demanda da primeira OSC', 'Demanda da segunda OSC'], $this->coluna($resposta, 'demandas.data', 'titulo'));

        Demanda::factory(15)->create();
        $this->get('/admin/demandas?pagina_demandas=2')
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->has('demandas.data', 2)
                ->where('demandas.meta.current_page', 2)
                ->where('demandas.meta.total', 17));
    }

    public function test_admin_can_register_an_osc_with_a_chosen_password(): void
    {
        $admin = Adm::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/oscs', $this->oscPayload([
                'password' => 'senhaOsc123',
                'password_confirmation' => 'senhaOsc123',
            ]))
            ->assertRedirect('/admin/oscs');

        $this->assertDatabaseHas('oscs', [
            'id_admin' => $admin->id_admin,
            'nome' => 'Nova Organização',
            'email' => 'nova@example.com',
        ]);
        $this->assertTrue(Hash::check('senhaOsc123', Osc::where('email', 'nova@example.com')->value('senha_hash')));
    }

    public function test_admin_can_generate_an_initial_osc_password(): void
    {
        $admin = Adm::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/oscs', $this->oscPayload())
            ->assertRedirect('/admin/oscs')
            ->assertSessionHas('senha_inicial');

        $senha = session('senha_inicial');
        $this->assertGreaterThanOrEqual(8, strlen($senha));
        $this->assertTrue(Hash::check($senha, Osc::where('email', 'nova@example.com')->value('senha_hash')));
    }

    public function test_osc_email_must_be_unique(): void
    {
        $admin = Adm::factory()->create();
        Osc::factory()->create(['email' => 'nova@example.com']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/oscs', $this->oscPayload())
            ->assertSessionHasErrors('email');
    }

    public function test_any_admin_can_edit_update_and_delete_any_osc(): void
    {
        $admin = Adm::factory()->create();
        $osc = Osc::factory()->create(['nome' => 'OSC editável']);
        $demanda = Demanda::factory()->for($osc)->create();

        $this->actingAs($admin, 'admin')
            ->get("/admin/oscs/{$osc->id_osc}/editar")
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Admin/OscForm')
                ->where('osc.nome', 'OSC editável'));

        $this->put("/admin/oscs/{$osc->id_osc}", $this->oscPayload(['nome' => 'OSC atualizada', 'email' => $osc->email]))
            ->assertRedirect('/admin/oscs');
        $this->assertDatabaseHas('oscs', ['id_osc' => $osc->id_osc, 'nome' => 'OSC atualizada']);

        $this->delete("/admin/oscs/{$osc->id_osc}")->assertRedirect('/admin/oscs');
        $this->assertDatabaseMissing('oscs', ['id_osc' => $osc->id_osc]);
        $this->assertDatabaseMissing('demandas', ['id_demanda' => $demanda->id_demanda]);
    }

    public function test_admin_can_moderate_any_demand(): void
    {
        $admin = Adm::factory()->create();
        $demanda = Demanda::factory()->create();

        $this->actingAs($admin, 'admin')
            ->patch("/admin/demandas/{$demanda->id_demanda}/status", ['status' => Demanda::STATUS_CONCLUIDA]);
        $this->assertSame(Demanda::STATUS_CONCLUIDA, $demanda->fresh()->status);

        $this->delete("/admin/demandas/{$demanda->id_demanda}");
        $this->assertDatabaseMissing('demandas', ['id_demanda' => $demanda->id_demanda]);
    }

    public function test_guests_and_oscs_cannot_access_admin_routes(): void
    {
        Adm::factory()->create();
        $demanda = Demanda::factory()->create();

        foreach (['/admin', '/admin/oscs', '/admin/oscs/criar', '/admin/demandas'] as $aba) {
            $this->get($aba)->assertRedirect('/admin/login');
        }
        $this->post('/admin/oscs', $this->oscPayload())->assertRedirect('/admin/login');

        $this->actingAs($demanda->osc, 'osc');
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->delete("/admin/demandas/{$demanda->id_demanda}")->assertRedirect('/admin/login');

        $this->assertDatabaseMissing('oscs', ['email' => 'nova@example.com']);
        $this->assertDatabaseHas('demandas', ['id_demanda' => $demanda->id_demanda]);
    }

    private function oscPayload(array $overrides = []): array
    {
        return $overrides + [
            'nome' => 'Nova Organização',
            'area_atuacao' => 'Educação',
            'descricao' => 'Atuação comunitária.',
            'endereco' => 'Camaçari',
            'telefone' => '(71) 99999-9999',
            'email' => 'nova@example.com',
        ];
    }
}
