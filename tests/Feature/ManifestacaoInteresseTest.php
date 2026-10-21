<?php

namespace Tests\Feature;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Support\Turnstile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ManifestacaoInteresseTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_an_interest_without_login_and_shows_the_success_page(): void
    {
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload())
            ->assertRedirect("/demandas/{$demanda->id_demanda}/obrigado");

        $this->assertDatabaseHas('manifestacoes_interesses', [
            'id_demanda' => $demanda->id_demanda,
            'nome_apoiador' => 'Maria Silva',
            'telefone' => '(71) 99999-9999',
            'email' => 'maria@example.com',
            'tipo_interesse' => 'Apoiador/Doação',
            'mensagem' => 'Posso ajudar aos sábados.',
        ]);

        $this->get("/demandas/{$demanda->id_demanda}/obrigado")
            ->assertOk()
            ->assertInertia(fn (Assert $pagina) => $pagina
                ->component('Publico/Obrigado')
                ->where('organizacao', $demanda->osc->nome));
    }

    public function test_success_page_requires_a_recent_submission(): void
    {
        $demanda = Demanda::factory()->create();

        $this->get("/demandas/{$demanda->id_demanda}/obrigado")
            ->assertRedirect("/demandas/{$demanda->id_demanda}");
    }

    public function test_it_requires_at_least_one_contact_method(): void
    {
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload(['telefone' => '', 'email' => '']))
            ->assertSessionHasErrors(['telefone', 'email']);

        $this->assertSame(0, ManifestacaoInteresse::count());
    }

    public function test_it_requires_consent_to_share_data(): void
    {
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload(['consentimento' => null]))
            ->assertSessionHasErrors('consentimento');
    }

    public function test_it_only_accepts_the_defined_interest_types(): void
    {
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload(['tipo_interesse' => 'Patrocinador']))
            ->assertSessionHasErrors('tipo_interesse');
    }

    public function test_the_same_contact_cannot_register_twice_for_the_same_demand(): void
    {
        $demanda = Demanda::factory()->create();
        $this->post($this->url($demanda), $this->payload());

        $this->post($this->url($demanda), $this->payload(['telefone' => '']))
            ->assertSessionHasErrors('email');

        $this->assertSame(1, ManifestacaoInteresse::count());
    }

    public function test_a_concluded_demand_does_not_accept_new_interests(): void
    {
        $demanda = Demanda::factory()->status(Demanda::STATUS_CONCLUIDA)->create();

        $this->post($this->url($demanda), $this->payload())->assertNotFound();

        $this->assertSame(0, ManifestacaoInteresse::count());
    }

    public function test_the_public_form_is_rate_limited(): void
    {
        $demanda = Demanda::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $this->post($this->url($demanda), $this->payload(['email' => "pessoa{$i}@example.com", 'telefone' => '']));
        }

        $this->post($this->url($demanda), $this->payload(['email' => 'extra@example.com', 'telefone' => '']))
            ->assertTooManyRequests();
    }

    public function test_bots_filling_the_honeypot_are_rejected(): void
    {
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, ManifestacaoInteresse::count());
    }

    public function test_submissions_faster_than_a_human_are_rejected(): void
    {
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload(['_carregado_em' => Crypt::encryptString((string) now()->getTimestamp())]))
            ->assertSessionHasErrors('_carregado_em');
        $this->post($this->url($demanda), $this->payload(['_carregado_em' => 'adulterado']))
            ->assertSessionHasErrors('_carregado_em');

        $this->assertSame(0, ManifestacaoInteresse::count());
    }

    public function test_turnstile_is_not_required_while_it_is_not_configured(): void
    {
        Http::fake();
        $demanda = Demanda::factory()->create();

        $this->get("/demandas/{$demanda->id_demanda}")
            ->assertInertia(fn (Assert $pagina) => $pagina->where('antiSpam.turnstileSiteKey', null));
        $this->post($this->url($demanda), $this->payload())->assertSessionHasNoErrors();

        Http::assertNothingSent();
    }

    public function test_turnstile_token_is_verified_when_configured(): void
    {
        $this->ativarTurnstile();
        Http::fake([Turnstile::VERIFICACAO => Http::response(['success' => true])]);
        $demanda = Demanda::factory()->create();

        $this->get("/demandas/{$demanda->id_demanda}")
            ->assertInertia(fn (Assert $pagina) => $pagina->where('antiSpam.turnstileSiteKey', 'chave-teste'));

        $this->post($this->url($demanda), $this->payload([Turnstile::CAMPO => 'token-valido']))
            ->assertRedirect("/demandas/{$demanda->id_demanda}/obrigado");

        Http::assertSent(fn ($request) => $request['secret'] === 'segredo-teste' && $request['response'] === 'token-valido');
    }

    public function test_an_invalid_or_missing_turnstile_token_is_rejected(): void
    {
        $this->ativarTurnstile();
        Http::fake([Turnstile::VERIFICACAO => Http::response(['success' => false])]);
        $demanda = Demanda::factory()->create();

        $this->post($this->url($demanda), $this->payload())->assertSessionHasErrors(Turnstile::CAMPO);
        $this->post($this->url($demanda), $this->payload([Turnstile::CAMPO => 'token-falso']))
            ->assertSessionHasErrors(Turnstile::CAMPO);

        $this->assertSame(0, ManifestacaoInteresse::count());
    }

    private function ativarTurnstile(): void
    {
        config([
            'services.turnstile.site_key' => 'chave-teste',
            'services.turnstile.secret_key' => 'segredo-teste',
        ]);
    }

    private function url(Demanda $demanda): string
    {
        return "/demandas/{$demanda->id_demanda}/interesse";
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'nome_apoiador' => 'Maria Silva',
            'telefone' => '(71) 99999-9999',
            'email' => 'maria@example.com',
            'tipo_interesse' => 'Apoiador/Doação',
            'mensagem' => 'Posso ajudar aos sábados.',
            'consentimento' => '1',
            'website' => '',
            '_carregado_em' => Crypt::encryptString((string) now()->subSeconds(10)->getTimestamp()),
        ];
    }
}
