<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentacaoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_docs_are_hidden_outside_local_unless_enabled(): void
    {
        config(['seguranca.docs_api_publica' => false]);

        $this->get('/docs/api')->assertForbidden();
        $this->get('/docs/api.json')->assertForbidden();
    }

    public function test_swagger_ui_and_openapi_specification(): void
    {
        config(['seguranca.docs_api_publica' => true]);

        $this->get('/docs/api')
            ->assertOk()
            ->assertSee('id="swagger-ui"', false)
            ->assertSee(route('docs.api.json'));

        $especificacao = $this->getJson('/docs/api.json')->assertOk()->json();

        $this->assertStringStartsWith('3.1', $especificacao['openapi']);
        $this->assertSame('API CONECTA_OSC', $especificacao['info']['title']);
        $this->assertSame(['Público', 'OSC', 'Administração'], array_column($especificacao['tags'], 'name'));
        $this->assertArrayHasKey('http', $especificacao['components']['securitySchemes']);

        $metodos = collect($especificacao['paths'])
            ->flatMap(fn (array $operacoes) => array_keys($operacoes))
            ->unique()->sort()->values()->all();
        $this->assertSame(['delete', 'get', 'patch', 'post', 'put'], $metodos);

        // Rotas públicas não exigem token; as demais exigem.
        $this->assertSame([], $especificacao['paths']['/v1/demandas']['get']['security']);
        $this->assertArrayNotHasKey('security', $especificacao['paths']['/v1/osc/demandas']['get']);
    }
}
