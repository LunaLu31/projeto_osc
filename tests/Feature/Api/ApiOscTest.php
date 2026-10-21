<?php

namespace Tests\Feature\Api;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use App\Services\MetricasOsc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiOscTest extends TestCase
{
    use RefreshDatabase;

    private Osc $osc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->osc = Osc::factory()->create();
        Sanctum::actingAs($this->osc, ['osc']);
    }

    public function test_full_crud_with_every_http_method(): void
    {
        // POST
        $id = $this->postJson('/api/v1/osc/demandas', $this->demanda(['urgencia' => true]))
            ->assertCreated()
            ->assertJsonPath('data.status', Demanda::STATUS_ABERTA)
            ->assertJsonPath('data.urgente', true)
            ->assertJsonPath('data.total_contatos', 0)
            ->json('data.id');

        // GET (coleção e item)
        $this->getJson('/api/v1/osc/demandas')->assertOk()->assertJsonPath('data.0.id', $id);
        $this->getJson("/api/v1/osc/demandas/{$id}")->assertOk()->assertJsonPath('data.titulo', 'Cestas básicas');

        // PATCH parcial: altera só o título; urgência permanece
        $this->patchJson("/api/v1/osc/demandas/{$id}", ['titulo' => 'Cestas de Natal'])
            ->assertOk()
            ->assertJsonPath('data.titulo', 'Cestas de Natal')
            ->assertJsonPath('data.urgente', true)
            ->assertJsonPath('data.quantidade', 12);

        // PUT completo: todos os campos obrigatórios; urgência ausente = false
        $this->putJson("/api/v1/osc/demandas/{$id}", ['titulo' => 'Incompleto'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['categoria', 'quantidade', 'descricao', 'prazo']);
        $this->putJson("/api/v1/osc/demandas/{$id}", $this->demanda(['titulo' => 'Substituída', 'quantidade' => 3]))
            ->assertOk()
            ->assertJsonPath('data.titulo', 'Substituída')
            ->assertJsonPath('data.urgente', false);

        // PATCH de status
        $this->patchJson("/api/v1/osc/demandas/{$id}/status", ['status' => Demanda::STATUS_CONCLUIDA])
            ->assertOk()
            ->assertJsonPath('data.status_rotulo', 'Concluída');

        // DELETE
        $this->deleteJson("/api/v1/osc/demandas/{$id}")->assertNoContent();
        $this->getJson("/api/v1/osc/demandas/{$id}")->assertNotFound();
    }

    public function test_demand_validation(): void
    {
        $this->postJson('/api/v1/osc/demandas', $this->demanda([
            'categoria' => 'Inexistente',
            'quantidade' => 0,
            'prazo' => now()->subDay()->toDateString(),
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['categoria', 'quantidade', 'prazo']);

        $id = Demanda::factory()->for($this->osc)->create()->id_demanda;
        $this->patchJson("/api/v1/osc/demandas/{$id}/status", ['status' => 'cancelada'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_cannot_touch_demands_of_another_osc(): void
    {
        $alheia = Demanda::factory()->create();
        $url = "/api/v1/osc/demandas/{$alheia->id_demanda}";

        $this->getJson($url)->assertForbidden();
        $this->putJson($url, $this->demanda())->assertForbidden();
        $this->patchJson($url, ['titulo' => 'Invasão'])->assertForbidden();
        $this->patchJson("{$url}/status", ['status' => Demanda::STATUS_CONCLUIDA])->assertForbidden();
        $this->deleteJson($url)->assertForbidden();

        $this->assertSame(Demanda::STATUS_ABERTA, $alheia->fresh()->status);
        $this->getJson('/api/v1/osc/demandas')->assertJsonCount(0, 'data');
    }

    public function test_lists_and_filters_only_its_own_contacts(): void
    {
        [$livros, $roupas] = Demanda::factory(2)->for($this->osc)->create();
        ManifestacaoInteresse::factory()->for($livros)->create(['nome_apoiador' => 'Ana', 'tipo_interesse' => 'Voluntário']);
        ManifestacaoInteresse::factory()->for($roupas)->create(['nome_apoiador' => 'Beto', 'tipo_interesse' => 'Aluno']);
        $alheia = Demanda::factory()->create();
        ManifestacaoInteresse::factory()->for($alheia)->create(['nome_apoiador' => 'Contato alheio']);

        $this->getJson('/api/v1/osc/contatos')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'nome', 'telefone', 'email', 'tipo_interesse', 'mensagem', 'recebido_em', 'demanda' => ['id', 'titulo']]]])
            ->assertDontSee('Contato alheio');

        $this->getJson("/api/v1/osc/contatos?demanda={$livros->id_demanda}")->assertJsonCount(1, 'data')->assertJsonPath('data.0.nome', 'Ana');
        $this->getJson('/api/v1/osc/contatos?tipo=Aluno')->assertJsonCount(1, 'data')->assertJsonPath('data.0.nome', 'Beto');
        $this->getJson("/api/v1/osc/contatos?demanda={$alheia->id_demanda}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('demanda');
    }

    public function test_dashboard_metrics(): void
    {
        $demanda = Demanda::factory()->for($this->osc)->create();
        Demanda::factory()->for($this->osc)->status(Demanda::STATUS_CONCLUIDA)->create();
        ManifestacaoInteresse::factory()->for($demanda)->create(['tipo_interesse' => 'Aluno']);

        $this->getJson('/api/v1/osc/painel')
            ->assertOk()
            ->assertJsonPath('data.demandas_ativas', 1)
            ->assertJsonPath('data.demandas_concluidas', 1)
            ->assertJsonPath('data.total_contatos', 1)
            ->assertJsonPath('data.contatos_por_tipo.Aluno', 1)
            ->assertJsonCount(MetricasOsc::MESES_GRAFICO, 'data.contatos_por_mes')
            ->assertJsonPath('data.contatos_por_mes.5', ['mes' => now()->format('Y-m'), 'total' => 1]);
    }

    public function test_change_password_revokes_other_tokens(): void
    {
        $outro = $this->osc->createToken('celular-antigo', ['osc']);
        $atual = $this->osc->createToken('este', ['osc']);
        $this->app['auth']->forgetGuards();

        $this->withToken($atual->plainTextToken)
            ->putJson('/api/v1/osc/perfil/senha', [
                'senha_atual' => 'senha-segura',
                'password' => 'novaSenha2026',
                'password_confirmation' => 'novaSenha2026',
            ])->assertNoContent();

        $this->assertTrue(Hash::check('novaSenha2026', $this->osc->fresh()->senha_hash));
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $outro->accessToken->id]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $atual->accessToken->id]);
    }

    private function demanda(array $sobrescrever = []): array
    {
        return $sobrescrever + [
            'titulo' => 'Cestas básicas',
            'categoria' => 'Alimentos',
            'quantidade' => 12,
            'descricao' => 'Cestas para famílias atendidas.',
            'prazo' => now()->addMonth()->toDateString(),
        ];
    }
}
