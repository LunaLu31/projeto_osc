<?php

namespace Tests;

use App\Auth\TokensJwt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // As views usam @vite; os testes não dependem de `npm run build`.
        $this->withoutVite();
    }

    /** JWT de login emitido direto pelo serviço (perfil "osc" ou "admin"), válido por 7 dias. */
    protected function tokenJwt(Authenticatable $usuario, string $perfil): string
    {
        return $this->app->make(TokensJwt::class)->emitir($usuario, $perfil, lembrar: true)->token;
    }

    /**
     * Lê uma prop da página Inertia (notação de ponto) e devolve uma coluna dela,
     * ex.: $this->coluna($resposta, 'contatos.data', 'nome').
     *
     * @return list<mixed>
     */
    protected function coluna(TestResponse $resposta, string $prop, string $campo): array
    {
        return collect(data_get($resposta->viewData('page')['props'], $prop))->pluck($campo)->all();
    }
}
