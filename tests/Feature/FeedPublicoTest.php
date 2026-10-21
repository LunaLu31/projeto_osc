<?php

namespace Tests\Feature;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Publico/Feed')
                ->where('metricas.total_oscs', 1)
                ->where('metricas.demandas_ativas', 2)
                ->where('metricas.demandas_concluidas', 1)
                ->where('metricas.total_manifestacoes', 1)
                ->where('metricas.manifestacoes_no_mes', 1)
                ->where('iniciaisOscsDestaque', ['OE'])
                // Props compartilhadas usadas pelos filtros e formulários.
                ->where('referencias.statusPublicos', Demanda::STATUS_PUBLICOS)
                ->where('auth.osc', null)
                ->has('ziggy.routes'));
    }

    public function test_feed_hides_concluded_demands_and_lists_urgent_first(): void
    {
        Demanda::factory()->create(['titulo' => 'Demanda comum']);
        Demanda::factory()->create(['titulo' => 'Demanda urgente', 'urgencia' => true]);
        Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create(['titulo' => 'Demanda concluída']);

        $this->get('/')->assertInertia(fn (Assert $pagina) => $pagina
            ->has('demandas.data', 2)
            ->where('demandas.data.0.titulo', 'Demanda urgente')
            ->where('demandas.data.0.urgente', true)
            ->where('demandas.data.1.titulo', 'Demanda comum')
            ->where('demandas.meta.total', 2));
    }

    public function test_feed_filters_by_category_status_urgency_and_search(): void
    {
        $osc = Osc::factory()->create(['nome' => 'Casa da Criança']);
        Demanda::factory()->for($osc)->create(['titulo' => 'Cestas básicas', 'categoria' => 'Alimentos']);
        Demanda::factory()->create(['titulo' => 'Kits de higiene', 'categoria' => 'Higiene', 'urgencia' => true]);
        Demanda::factory()->status(Demanda::STATUS_PARCIAL)->create(['titulo' => 'Agasalhos', 'categoria' => 'Vestuário']);

        $titulos = fn (string $url) => collect($this->get($url)->viewData('page')['props']['demandas']['data'])->pluck('titulo')->all();

        $this->assertSame(['Kits de higiene'], $titulos('/?categoria=Higiene'));
        $this->assertSame(['Agasalhos'], $titulos('/?status=Parcial'));
        $this->assertSame(['Kits de higiene'], $titulos('/?urgente=1'));
        $this->assertSame(['Cestas básicas'], $titulos('/?busca=Casa+da'));

        $this->get('/?categoria=Higiene&urgente=1')->assertInertia(fn (Assert $pagina) => $pagina
            ->where('filtros.categoria', 'Higiene')
            ->where('filtros.urgente', true));
    }

    public function test_invalid_filters_are_ignored(): void
    {
        Demanda::factory()->create(['titulo' => 'Cestas básicas']);

        $this->get('/?categoria=Inexistente&status=Concluída')
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina->has('demandas.data', 1));
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

    public function test_demand_detail_page_shows_the_demand_and_anti_spam_data(): void
    {
        $demanda = Demanda::factory()->create(['titulo' => 'Material escolar']);

        $this->get("/demandas/{$demanda->id_demanda}")
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Publico/Demanda')
                ->where('demanda.titulo', 'Material escolar')
                ->where('demanda.osc.nome', $demanda->osc->nome)
                ->missing('demanda.osc.email')
                ->missing('demanda.total_contatos')
                ->has('antiSpam.carregadoEm')
                ->where('antiSpam.turnstileSiteKey', null));
    }

    public function test_concluded_or_missing_demands_are_not_publicly_available(): void
    {
        $concluida = Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create();

        $this->get("/demandas/{$concluida->id_demanda}")->assertNotFound();
        $this->get('/demandas/999999')
            ->assertNotFound()
            ->assertInertia(fn (Assert $pagina) => $pagina->component('Erro')->where('status', 404));
    }

    public function test_json_errors_use_friendly_messages(): void
    {
        $this->getJson('/demandas/999999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'O registro solicitado não foi encontrado.']);
    }
}
