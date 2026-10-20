<?php

namespace Tests\Feature;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_metrics_reflect_database_records(): void
    {
        $osc = Osc::factory()->create(['nome' => 'Organização Exemplo']);
        $aberta = Demanda::factory()->for($osc)->create();
        Demanda::factory()->for($osc)->status(Demanda::STATUS_PARCIAL)->create();
        Demanda::factory()->for($osc)->status(Demanda::STATUS_CONCLUIDA)->create();
        ManifestacaoInteresse::factory()->for($aberta)->create();

        $this->get('/')
            ->assertOk()
            ->assertViewIs('publico.feed')
            ->assertViewHas('totalOscs', 1)
            ->assertViewHas('demandasAtivas', 2)
            ->assertViewHas('demandasConcluidas', 1)
            ->assertViewHas('totalManifestacoes', 1)
            ->assertViewHas('manifestacoesNoMes', 1)
            ->assertViewHas('iniciaisOscsDestaque', fn ($iniciais) => $iniciais->all() === ['OE']);
    }

    public function test_feed_hides_concluded_demands_and_lists_urgent_first(): void
    {
        Demanda::factory()->create(['titulo' => 'Demanda comum']);
        Demanda::factory()->create(['titulo' => 'Demanda urgente', 'urgencia' => true]);
        Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create(['titulo' => 'Demanda concluída']);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Demanda urgente', 'Demanda comum'])
            ->assertDontSee('Demanda concluída');
    }

    public function test_feed_filters_by_category_status_urgency_and_search(): void
    {
        $osc = Osc::factory()->create(['nome' => 'Casa da Criança']);
        Demanda::factory()->for($osc)->create(['titulo' => 'Cestas básicas', 'categoria' => 'Alimentos']);
        Demanda::factory()->create(['titulo' => 'Kits de higiene', 'categoria' => 'Higiene', 'urgencia' => true]);
        Demanda::factory()->status(Demanda::STATUS_PARCIAL)->create(['titulo' => 'Agasalhos', 'categoria' => 'Vestuário']);

        $this->get('/?categoria=Higiene')->assertSee('Kits de higiene')->assertDontSee('Cestas básicas');
        $this->get('/?status=Parcial')->assertSee('Agasalhos')->assertDontSee('Cestas básicas');
        $this->get('/?urgente=1')->assertSee('Kits de higiene')->assertDontSee('Agasalhos');
        $this->get('/?busca=Casa+da')->assertSee('Cestas básicas')->assertDontSee('Kits de higiene');
    }

    public function test_invalid_filters_are_ignored(): void
    {
        Demanda::factory()->create(['titulo' => 'Cestas básicas']);

        $this->get('/?categoria=Inexistente&status=Concluída')
            ->assertOk()
            ->assertSee('Cestas básicas');
    }

    public function test_public_pages_never_expose_supporter_contacts(): void
    {
        $demanda = Demanda::factory()->create();
        ManifestacaoInteresse::factory()->for($demanda)->create([
            'telefone' => '(71) 98888-7777',
            'email' => 'apoiador.secreto@example.com',
        ]);

        foreach (['/', "/demandas/{$demanda->id_demanda}"] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSee('apoiador.secreto@example.com')
                ->assertDontSee('98888-7777');
        }
    }

    public function test_demand_detail_page_shows_the_demand_and_the_interest_form(): void
    {
        $demanda = Demanda::factory()->create(['titulo' => 'Material escolar']);

        $this->get("/demandas/{$demanda->id_demanda}")
            ->assertOk()
            ->assertViewIs('publico.demanda')
            ->assertSee('Material escolar')
            ->assertSee($demanda->osc->nome)
            ->assertSee('Apoiador/Doação');
    }

    public function test_concluded_or_missing_demands_are_not_publicly_available(): void
    {
        $concluida = Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create();

        $this->get("/demandas/{$concluida->id_demanda}")->assertNotFound();
        $this->get('/demandas/999999')->assertNotFound();
    }

    public function test_json_errors_use_friendly_messages(): void
    {
        $this->getJson('/demandas/999999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'O registro solicitado não foi encontrado.']);
    }
}
