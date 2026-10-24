<?php

namespace Tests\Feature\Api;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAdminTest extends TestCase
{
    use RefreshDatabase;

    private Adm $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Adm::factory()->create();
        $this->actingAs($this->admin, 'api_admin');
    }

    public function test_osc_crud_with_every_http_method(): void
    {
        // POST com senha gerada pelo sistema
        $resposta = $this->postJson('/api/v1/admin/oscs', $this->osc())
            ->assertCreated()
            ->assertJsonPath('data.nome', 'Nova Organização')
            ->assertJsonMissingPath('data.senha_hash');
        $id = $resposta->json('data.id');
        $this->assertTrue(Hash::check($resposta->json('senha_inicial'), Osc::find($id)->senha_hash));

        // GET
        $this->getJson('/api/v1/admin/oscs?busca=Nova')->assertOk()->assertJsonPath('data.0.id', $id);
        $this->getJson("/api/v1/admin/oscs/{$id}")->assertOk()->assertJsonPath('data.total_demandas', 0);

        // PATCH parcial e PUT completo
        $this->patchJson("/api/v1/admin/oscs/{$id}", ['telefone' => '(71) 3333-0000'])
            ->assertOk()
            ->assertJsonPath('data.telefone', '(71) 3333-0000')
            ->assertJsonPath('data.nome', 'Nova Organização');
        $this->putJson("/api/v1/admin/oscs/{$id}", ['nome' => 'Só o nome'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['area_atuacao', 'email']);
        $this->putJson("/api/v1/admin/oscs/{$id}", $this->osc(['nome' => 'Renomeada']))
            ->assertOk()
            ->assertJsonPath('data.nome', 'Renomeada');

        // DELETE
        $this->deleteJson("/api/v1/admin/oscs/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('oscs', ['id_osc' => $id]);
    }

    public function test_osc_with_chosen_password_and_unique_email(): void
    {
        $this->postJson('/api/v1/admin/oscs', $this->osc(['password' => 'senhaOsc123', 'password_confirmation' => 'senhaOsc123']))
            ->assertCreated()
            ->assertJsonPath('senha_inicial', null);

        $this->postJson('/api/v1/admin/oscs', $this->osc())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_resetting_an_osc_password_revokes_its_tokens(): void
    {
        $osc = Osc::factory()->create();
        $tokenDaOsc = $this->tokenJwt($osc, 'osc');

        $this->patchJson("/api/v1/admin/oscs/{$osc->id_osc}", [
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
        ])->assertOk();

        $this->app['auth']->forgetGuards();
        $this->withToken($tokenDaOsc)->getJson('/api/v1/osc/perfil')->assertUnauthorized();
    }

    public function test_moderates_any_demand(): void
    {
        $demanda = Demanda::factory()->create(['titulo' => 'Doação de livros']);
        Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create(['titulo' => 'Antiga']);

        $this->getJson('/api/v1/admin/demandas')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/admin/demandas?status=Concluída')->assertJsonCount(1, 'data')->assertJsonPath('data.0.titulo', 'Antiga');
        $this->getJson("/api/v1/admin/demandas?osc={$demanda->id_osc}")->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/admin/demandas/{$demanda->id_demanda}")->assertOk()->assertJsonPath('data.osc.id', $demanda->id_osc);

        $this->patchJson("/api/v1/admin/demandas/{$demanda->id_demanda}/status", ['status' => Demanda::STATUS_PARCIAL])
            ->assertOk()
            ->assertJsonPath('data.status', Demanda::STATUS_PARCIAL);

        $this->deleteJson("/api/v1/admin/demandas/{$demanda->id_demanda}")->assertNoContent();
        $this->assertDatabaseMissing('demandas', ['id_demanda' => $demanda->id_demanda]);
    }

    public function test_global_metrics(): void
    {
        Demanda::factory(2)->create();

        $this->getJson('/api/v1/admin/painel')
            ->assertOk()
            ->assertJsonPath('data.total_oscs', 2)
            ->assertJsonPath('data.demandas_por_status.Aberta', 2);
    }

    private function osc(array $sobrescrever = []): array
    {
        return $sobrescrever + [
            'nome' => 'Nova Organização',
            'area_atuacao' => 'Educação',
            'descricao' => 'Atuação comunitária.',
            'endereco' => 'Camaçari - BA',
            'telefone' => '(71) 99999-9999',
            'email' => 'nova@example.com',
        ];
    }
}
