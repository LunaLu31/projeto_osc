<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AreaOscTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_available_to_guests(): void
    {
        $this->get('/osc/login')->assertOk()->assertViewIs('osc.login');
    }

    public function test_an_osc_can_log_in_with_its_registered_credentials(): void
    {
        $osc = Osc::factory()->create();

        $this->post('/osc/login', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertRedirect('/osc/painel');

        $this->assertAuthenticatedAs($osc, 'osc');
    }

    public function test_an_osc_cannot_log_in_with_an_invalid_password(): void
    {
        $osc = Osc::factory()->create();

        $this->from('/osc/login')
            ->post('/osc/login', ['email' => $osc->email, 'password' => 'senha-incorreta'])
            ->assertRedirect('/osc/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest('osc');
    }

    public function test_login_is_blocked_after_too_many_attempts(): void
    {
        $osc = Osc::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/osc/login', ['email' => $osc->email, 'password' => 'errada']);
        }

        $this->post('/osc/login', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('osc');
    }

    public function test_an_authenticated_osc_can_log_out(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')->post('/osc/logout')->assertRedirect('/');

        $this->assertGuest('osc');
    }

    public function test_guests_are_redirected_to_the_osc_login(): void
    {
        foreach (['/osc/painel', '/osc/demandas', '/osc/contatos', '/osc/senha'] as $url) {
            $this->get($url)->assertRedirect('/osc/login');
        }
        $this->post('/osc/demandas', $this->demandPayload())->assertRedirect('/osc/login');

        $this->assertDatabaseMissing('demandas', ['titulo' => 'Cestas básicas']);
    }

    public function test_an_admin_session_cannot_access_the_osc_area(): void
    {
        $admin = Adm::factory()->create();

        $this->actingAs($admin, 'admin')->get('/osc/painel')->assertRedirect('/osc/login');
    }

    public function test_overview_shows_metrics_recent_contacts_and_upcoming_deadlines(): void
    {
        $osc = Osc::factory()->create();
        $demanda = Demanda::factory()->for($osc)->create(['titulo' => 'Doação de livros', 'prazo' => now()->addDays(3)]);
        Demanda::factory()->for($osc)->status(Demanda::STATUS_CONCLUIDA)->create();
        Demanda::factory()->create(['titulo' => 'Demanda de outra OSC', 'prazo' => now()->addDays(2)]);
        ManifestacaoInteresse::factory()->for($demanda)->create(['nome_apoiador' => 'Ana Souza', 'tipo_interesse' => 'Voluntário']);

        $this->actingAs($osc, 'osc')
            ->get('/osc/painel')
            ->assertOk()
            ->assertViewIs('osc.painel')
            ->assertViewHas('metricas', fn (array $m) => $m['demandas_ativas'] === 1
                && $m['demandas_concluidas'] === 1
                && $m['total_contatos'] === 1
                && $m['contatos_recentes'] === 1
                && $m['contatos_por_tipo']['Voluntário'] === 1)
            ->assertSee('Ana Souza')
            ->assertSee('Doação de livros')
            ->assertDontSee('Demanda de outra OSC');
    }

    public function test_demands_page_lists_only_its_demands_and_filters_by_status(): void
    {
        $osc = Osc::factory()->create();
        Demanda::factory()->for($osc)->create(['titulo' => 'Demanda aberta']);
        Demanda::factory()->for($osc)->status(Demanda::STATUS_CONCLUIDA)->create(['titulo' => 'Demanda finalizada']);
        Demanda::factory()->create(['titulo' => 'Demanda de outra OSC']);

        $this->actingAs($osc, 'osc');
        $this->get('/osc/demandas')
            ->assertOk()
            ->assertSee('Demanda aberta')
            ->assertSee('Demanda finalizada')
            ->assertDontSee('Demanda de outra OSC');

        $this->get('/osc/demandas?status='.urlencode(Demanda::STATUS_CONCLUIDA))
            ->assertSee('Demanda finalizada')
            ->assertDontSee('Demanda aberta');
    }

    public function test_contacts_page_shows_only_its_own_supporters(): void
    {
        $osc = Osc::factory()->create();
        $demanda = Demanda::factory()->for($osc)->create(['titulo' => 'Doação de livros']);
        ManifestacaoInteresse::factory()->for($demanda)->create([
            'nome_apoiador' => 'Ana Souza',
            'email' => 'ana@example.com',
            'tipo_interesse' => 'Voluntário',
        ]);
        ManifestacaoInteresse::factory()->create(['nome_apoiador' => 'Contato de outra OSC', 'email' => 'outra@example.com']);

        $this->actingAs($osc, 'osc')
            ->get('/osc/contatos')
            ->assertOk()
            ->assertViewIs('osc.contatos')
            ->assertSee('Ana Souza')
            ->assertSee('ana@example.com')
            ->assertSee('Voluntário')
            ->assertSee('Doação de livros')
            ->assertDontSee('Contato de outra OSC')
            ->assertDontSee('outra@example.com');
    }

    public function test_contacts_can_be_filtered_by_demand_and_type(): void
    {
        $osc = Osc::factory()->create();
        [$livros, $roupas] = Demanda::factory(2)->for($osc)->create();
        ManifestacaoInteresse::factory()->for($livros)->create(['nome_apoiador' => 'Ana Livros', 'tipo_interesse' => 'Voluntário']);
        ManifestacaoInteresse::factory()->for($roupas)->create(['nome_apoiador' => 'Beto Roupas', 'tipo_interesse' => 'Aluno']);
        $outraDemanda = Demanda::factory()->create();
        ManifestacaoInteresse::factory()->for($outraDemanda)->create(['nome_apoiador' => 'Contato alheio']);

        $this->actingAs($osc, 'osc');
        $this->get("/osc/contatos?demanda={$livros->id_demanda}")->assertSee('Ana Livros')->assertDontSee('Beto Roupas');
        $this->get('/osc/contatos?tipo=Aluno')->assertSee('Beto Roupas')->assertDontSee('Ana Livros');

        // Filtrar por demanda de outra OSC é ignorado e nunca expõe os contatos dela.
        $this->get("/osc/contatos?demanda={$outraDemanda->id_demanda}")
            ->assertSee('Ana Livros')
            ->assertDontSee('Contato alheio');
    }

    public function test_an_osc_can_create_a_demand(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')->get('/osc/demandas/criar')->assertOk();

        $this->post('/osc/demandas', $this->demandPayload(['urgencia' => '1']))
            ->assertRedirect('/osc/demandas');

        $this->assertDatabaseHas('demandas', [
            'id_osc' => $osc->id_osc,
            'titulo' => 'Cestas básicas',
            'status' => Demanda::STATUS_ABERTA,
            'urgencia' => true,
        ]);
    }

    public function test_a_demand_without_the_urgent_checkbox_is_not_urgent(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')->post('/osc/demandas', $this->demandPayload());

        $this->assertDatabaseHas('demandas', ['titulo' => 'Cestas básicas', 'urgencia' => false]);
    }

    public function test_demand_validation_rejects_invalid_data(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')
            ->post('/osc/demandas', $this->demandPayload([
                'categoria' => 'Inexistente',
                'quantidade' => 0,
                'prazo' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors(['categoria', 'quantidade', 'prazo']);
    }

    public function test_an_osc_can_edit_update_status_and_delete_its_own_demand(): void
    {
        $osc = Osc::factory()->create();
        $demanda = Demanda::factory()->for($osc)->create();

        $this->actingAs($osc, 'osc')
            ->get("/osc/demandas/{$demanda->id_demanda}/editar")
            ->assertOk()
            ->assertSee($demanda->titulo);

        $this->put("/osc/demandas/{$demanda->id_demanda}", $this->demandPayload(['titulo' => 'Título editado']))
            ->assertRedirect('/osc/demandas');
        $this->assertSame('Título editado', $demanda->fresh()->titulo);

        $this->patch("/osc/demandas/{$demanda->id_demanda}/status", ['status' => Demanda::STATUS_PARCIAL]);
        $this->assertSame(Demanda::STATUS_PARCIAL, $demanda->fresh()->status);

        $this->delete("/osc/demandas/{$demanda->id_demanda}")->assertRedirect('/osc/demandas');
        $this->assertDatabaseMissing('demandas', ['id_demanda' => $demanda->id_demanda]);
    }

    public function test_status_must_be_one_of_the_allowed_values(): void
    {
        $osc = Osc::factory()->create();
        $demanda = Demanda::factory()->for($osc)->create();

        $this->actingAs($osc, 'osc')
            ->patch("/osc/demandas/{$demanda->id_demanda}/status", ['status' => 'cancelada'])
            ->assertSessionHasErrors('status');
    }

    public function test_an_osc_cannot_manage_a_demand_from_another_osc(): void
    {
        $osc = Osc::factory()->create();
        $demandaDeOutra = Demanda::factory()->create();
        $id = $demandaDeOutra->id_demanda;

        $this->actingAs($osc, 'osc');
        $this->get("/osc/demandas/{$id}/editar")->assertForbidden();
        $this->put("/osc/demandas/{$id}", $this->demandPayload())->assertForbidden();
        $this->patch("/osc/demandas/{$id}/status", ['status' => Demanda::STATUS_CONCLUIDA])->assertForbidden();
        $this->delete("/osc/demandas/{$id}")->assertForbidden();

        $this->assertSame(Demanda::STATUS_ABERTA, $demandaDeOutra->fresh()->status);
    }

    public function test_an_osc_can_change_its_password(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')->get('/osc/senha')->assertOk();

        $this->put('/osc/senha', [
            'senha_atual' => 'senha-segura',
            'password' => 'novaSenha2026',
            'password_confirmation' => 'novaSenha2026',
        ])->assertRedirect('/osc/painel');

        $this->assertTrue(Hash::check('novaSenha2026', $osc->fresh()->senha_hash));
    }

    public function test_password_change_requires_the_current_password_and_a_strong_password(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')
            ->put('/osc/senha', [
                'senha_atual' => 'errada',
                'password' => 'curta',
                'password_confirmation' => 'curta',
            ])
            ->assertSessionHasErrors(['senha_atual', 'password']);
    }

    private function demandPayload(array $overrides = []): array
    {
        return $overrides + [
            'titulo' => 'Cestas básicas',
            'categoria' => 'Alimentos',
            'quantidade' => 12,
            'descricao' => 'Cestas para famílias atendidas.',
            'prazo' => now()->addMonth()->toDateString(),
        ];
    }
}
