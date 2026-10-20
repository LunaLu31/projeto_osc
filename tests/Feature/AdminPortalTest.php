<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_without_admins_the_portal_leads_to_the_initial_setup(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertRedirect('/admin/configuracao');
        $this->get('/admin/configuracao')->assertOk()->assertViewIs('admin.configuracao');
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

        $this->get('/admin/login')->assertOk()->assertViewIs('admin.login');
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

    public function test_dashboard_shows_global_metrics_and_every_osc_of_the_network(): void
    {
        $admin = Adm::factory()->create();
        $ownedOsc = Osc::factory()->for($admin, 'adm')->create(['nome' => 'OSC cadastrada por mim']);
        $otherOsc = Osc::factory()->create(['nome' => 'OSC de outro admin']);
        Demanda::factory()->for($ownedOsc)->create(['titulo' => 'Demanda da primeira OSC']);
        Demanda::factory()->for($otherOsc)->status(Demanda::STATUS_CONCLUIDA)->create(['titulo' => 'Demanda da segunda OSC']);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk()
            ->assertViewIs('admin.painel')
            ->assertViewHas('oscs', fn ($oscs) => $oscs->count() === 2)
            ->assertViewHas('demandas', fn ($demandas) => $demandas->count() === 2)
            ->assertSee('OSC cadastrada por mim')
            ->assertSee('OSC de outro admin')
            ->assertSee('Demanda da primeira OSC')
            ->assertSee('Demanda da segunda OSC');
    }

    public function test_admin_can_register_an_osc_with_a_chosen_password(): void
    {
        $admin = Adm::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/oscs', $this->oscPayload([
                'password' => 'senhaOsc123',
                'password_confirmation' => 'senhaOsc123',
            ]))
            ->assertRedirect('/admin');

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
            ->assertRedirect('/admin')
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
            ->assertSee('OSC editável');

        $this->put("/admin/oscs/{$osc->id_osc}", $this->oscPayload(['nome' => 'OSC atualizada', 'email' => $osc->email]))
            ->assertRedirect('/admin');
        $this->assertDatabaseHas('oscs', ['id_osc' => $osc->id_osc, 'nome' => 'OSC atualizada']);

        $this->delete("/admin/oscs/{$osc->id_osc}")->assertRedirect('/admin');
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

        $this->get('/admin')->assertRedirect('/admin/login');
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
