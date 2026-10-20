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
        $this->get('/osc/painel')->assertRedirect('/osc/login');
        $this->post('/osc/demandas', $this->demandPayload())->assertRedirect('/osc/login');

        $this->assertDatabaseMissing('demandas', ['titulo' => 'Cestas básicas']);
    }

    public function test_an_admin_session_cannot_access_the_osc_area(): void
    {
        $admin = Adm::factory()->create();

        $this->actingAs($admin, 'admin')->get('/osc/painel')->assertRedirect('/osc/login');
    }

    public function test_dashboard_lists_only_its_demands_with_interest_contacts(): void
    {
        $osc = Osc::factory()->create();
        $demanda = Demanda::factory()->for($osc)->create(['titulo' => 'Doação de livros']);
        Demanda::factory()->create(['titulo' => 'Demanda de outra OSC']);
        ManifestacaoInteresse::factory()->for($demanda)->create([
            'nome_apoiador' => 'Ana Souza',
            'email' => 'ana@example.com',
            'tipo_interesse' => 'Voluntário',
        ]);

        $this->actingAs($osc, 'osc')
            ->get('/osc/painel')
            ->assertOk()
            ->assertViewIs('osc.painel')
            ->assertSee('Doação de livros')
            ->assertDontSee('Demanda de outra OSC')
            ->assertSee('Ana Souza')
            ->assertSee('Voluntário')
            ->assertSee('ana@example.com');
    }

    public function test_an_osc_can_create_a_demand(): void
    {
        $osc = Osc::factory()->create();

        $this->actingAs($osc, 'osc')->get('/osc/demandas/criar')->assertOk();

        $this->post('/osc/demandas', $this->demandPayload(['urgencia' => '1']))
            ->assertRedirect('/osc/painel');

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
            ->assertRedirect('/osc/painel');
        $this->assertSame('Título editado', $demanda->fresh()->titulo);

        $this->patch("/osc/demandas/{$demanda->id_demanda}/status", ['status' => Demanda::STATUS_PARCIAL]);
        $this->assertSame(Demanda::STATUS_PARCIAL, $demanda->fresh()->status);

        $this->delete("/osc/demandas/{$demanda->id_demanda}")->assertRedirect('/osc/painel');
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
