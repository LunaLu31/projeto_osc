<?php

namespace Tests\Feature\Api;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_public_demands_urgent_first_with_pagination(): void
    {
        Demanda::factory()->create(['titulo' => 'Comum']);
        Demanda::factory()->create(['titulo' => 'Urgente', 'urgencia' => true]);
        Demanda::factory()->status(Demanda::STATUS_PARCIAL)->create(['titulo' => 'Parcial']);
        Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create(['titulo' => 'Concluída']);

        $this->getJson('/api/v1/demandas?por_pagina=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.titulo', 'Urgente')
            ->assertJsonPath('data.0.urgente', true)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonStructure([
                'data' => [['id', 'titulo', 'categoria', 'descricao', 'quantidade', 'prazo', 'status', 'status_rotulo', 'urgente', 'osc' => ['id', 'nome', 'iniciais']]],
                'links', 'meta',
            ]);
    }

    public function test_filters_demands_and_validates_filter_values(): void
    {
        $osc = Osc::factory()->create(['nome' => 'Casa da Criança']);
        Demanda::factory()->for($osc)->create(['titulo' => 'Cestas', 'categoria' => 'Alimentos']);
        Demanda::factory()->create(['titulo' => 'Kits', 'categoria' => 'Higiene', 'urgencia' => true]);

        $this->getJson('/api/v1/demandas?categoria=Higiene')->assertJsonCount(1, 'data')->assertJsonPath('data.0.titulo', 'Kits');
        $this->getJson('/api/v1/demandas?urgente=1')->assertJsonCount(1, 'data')->assertJsonPath('data.0.titulo', 'Kits');
        $this->getJson('/api/v1/demandas?busca=Casa')->assertJsonCount(1, 'data')->assertJsonPath('data.0.titulo', 'Cestas');

        $this->getJson('/api/v1/demandas?status=Concluída&categoria=Inexistente')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status', 'categoria']);
    }

    public function test_shows_a_public_demand_and_hides_concluded_ones(): void
    {
        $aberta = Demanda::factory()->create(['titulo' => 'Material escolar']);
        $concluida = Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create();

        $this->getJson("/api/v1/demandas/{$aberta->id_demanda}")
            ->assertOk()
            ->assertJsonPath('data.titulo', 'Material escolar')
            ->assertJsonPath('data.osc.nome', $aberta->osc->nome)
            ->assertJsonMissingPath('data.osc.email')
            ->assertJsonMissingPath('data.total_contatos');

        $this->getJson("/api/v1/demandas/{$concluida->id_demanda}")
            ->assertNotFound()
            ->assertExactJson(['message' => 'O registro solicitado não foi encontrado.']);
    }

    public function test_public_endpoints_never_expose_supporter_contacts(): void
    {
        $demanda = Demanda::factory()->create();
        ManifestacaoInteresse::factory()->for($demanda)->create(['email' => 'segredo@example.com', 'telefone' => '(71) 97777-0000']);

        foreach (['/api/v1/demandas', "/api/v1/demandas/{$demanda->id_demanda}", '/api/v1/rede/estatisticas'] as $url) {
            $this->getJson($url)->assertOk()->assertDontSee('segredo@example.com')->assertDontSee('97777-0000');
        }
    }

    public function test_registers_interest_without_login(): void
    {
        $demanda = Demanda::factory()->create();

        $this->postJson("/api/v1/demandas/{$demanda->id_demanda}/interesses", $this->interesse())
            ->assertCreated()
            ->assertJsonPath('data.demanda_id', $demanda->id_demanda);

        $this->assertDatabaseHas('manifestacoes_interesses', [
            'id_demanda' => $demanda->id_demanda,
            'email' => 'maria@example.com',
            'tipo_interesse' => 'Voluntário',
        ]);
    }

    public function test_interest_validation_duplicates_and_concluded_demands(): void
    {
        $demanda = Demanda::factory()->create();
        $url = "/api/v1/demandas/{$demanda->id_demanda}/interesses";

        $this->postJson($url, $this->interesse(['email' => null, 'telefone' => null, 'consentimento' => false]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'telefone', 'consentimento']);

        $this->postJson($url, $this->interesse())->assertCreated();
        $this->postJson($url, $this->interesse())->assertUnprocessable()->assertJsonValidationErrors('email');

        $concluida = Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create();
        $this->postJson("/api/v1/demandas/{$concluida->id_demanda}/interesses", $this->interesse())->assertNotFound();
    }

    public function test_network_statistics_and_reference_values(): void
    {
        Demanda::factory()->create();
        Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create();

        $this->getJson('/api/v1/rede/estatisticas')
            ->assertOk()
            ->assertJsonPath('data.total_oscs', 2)
            ->assertJsonPath('data.demandas_ativas', 1)
            ->assertJsonPath('data.demandas_concluidas', 1);

        $this->getJson('/api/v1/rede/metadados')
            ->assertOk()
            ->assertJsonPath('data.tipos_interesse', ManifestacaoInteresse::TIPOS)
            ->assertJsonPath('data.status.1', ['valor' => 'Parcial', 'rotulo' => 'Parcialmente atendida']);
    }

    public function test_errors_are_json_in_portuguese_even_without_accept_header(): void
    {
        $this->get('/api/v1/inexistente')
            ->assertNotFound()
            ->assertExactJson(['message' => 'O registro solicitado não foi encontrado.']);

        $this->delete('/api/v1/demandas')
            ->assertStatus(405)
            ->assertExactJson(['message' => 'Método HTTP não permitido para este endereço.'])
            ->assertHeader('Allow');
    }

    public function test_responses_include_security_headers_and_cors(): void
    {
        $this->getJson('/api/v1/rede/metadados', ['Origin' => 'https://app.exemplo.org'])
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Access-Control-Allow-Origin');
    }

    private function interesse(array $sobrescrever = []): array
    {
        return $sobrescrever + [
            'nome_apoiador' => 'Maria Silva',
            'email' => 'maria@example.com',
            'telefone' => null,
            'tipo_interesse' => 'Voluntário',
            'mensagem' => 'Posso ajudar aos sábados.',
            'consentimento' => true,
        ];
    }
}
