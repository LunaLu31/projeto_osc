<?php

namespace Tests;

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
