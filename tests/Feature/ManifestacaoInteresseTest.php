<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManifestacaoInteresseTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_an_interest_for_an_existing_demand(): void
    {
        $demanda = $this->createDemanda();

        $response = $this->postJson('/manifestar', [
            'id_demanda' => $demanda->id_demanda,
            'nome_apoiador' => 'Maria Silva',
            'telefone' => '(71) 99999-9999',
            'email' => 'maria@example.com',
            'tipo_interesse' => 'Voluntário',
            'mensagem' => 'Posso ajudar aos sábados.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('manifestacoes_interesses', [
            'id_demanda' => $demanda->id_demanda,
            'nome_apoiador' => 'Maria Silva',
            'telefone' => '(71) 99999-9999',
            'email' => 'maria@example.com',
            'tipo_interesse' => 'Voluntário',
            'mensagem' => 'Posso ajudar aos sábados.',
        ]);
    }

    public function test_it_requires_at_least_one_contact_method(): void
    {
        $demanda = $this->createDemanda();

        $response = $this->postJson('/manifestar', [
            'id_demanda' => $demanda->id_demanda,
            'nome_apoiador' => 'Maria Silva',
            'tipo_interesse' => 'Voluntário',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['telefone', 'email']);
        $this->assertSame(0, ManifestacaoInteresse::count());
    }

    private function createDemanda(): Demanda
    {
        $admin = Adm::create([
            'nome' => 'Administrador',
            'email' => 'admin@example.com',
            'senha_hash' => 'hash',
        ]);

        $osc = Osc::create([
            'id_admin' => $admin->id_admin,
            'nome' => 'Organização Exemplo',
            'email' => 'osc@example.com',
            'senha_hash' => 'hash',
        ]);

        return Demanda::create([
            'id_osc' => $osc->id_osc,
            'titulo' => 'Apoio escolar',
            'status' => 'aberta',
        ]);
    }
}
