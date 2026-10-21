<?php

namespace Tests\Feature;

use App\Models\Demanda;
use App\Support\Turnstile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SegurancaTest extends TestCase
{
    use RefreshDatabase;

    private string $publicoTemporario;

    private string $hotFile;

    protected function setUp(): void
    {
        parent::setUp();

        // Isola os testes de um `npm run dev` rodando na máquina (arquivo public/hot).
        $this->publicoTemporario = storage_path('framework/testing/public');
        File::deleteDirectory($this->publicoTemporario);
        File::ensureDirectoryExists($this->publicoTemporario);
        $this->app->usePublicPath($this->publicoTemporario);
        $this->hotFile = public_path('hot');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicoTemporario);

        parent::tearDown();
    }

    public function test_every_response_has_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeaderMissing('Strict-Transport-Security');

        $this->assertStringContainsString('geolocation=()', $response->headers->get('Permissions-Policy'));
    }

    public function test_content_security_policy_uses_a_fresh_nonce_and_blocks_framing(): void
    {
        $primeira = $this->get('/')->headers->get('Content-Security-Policy');
        $segunda = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[A-Za-z0-9]+'/", $primeira);
        $this->assertStringContainsString("frame-ancestors 'none'", $primeira);
        $this->assertStringContainsString("object-src 'none'", $primeira);
        $this->assertStringContainsString("form-action 'self'", $primeira);
        $this->assertStringNotContainsString('unsafe-inline', $primeira);
        $this->assertStringNotContainsString(Turnstile::ORIGEM, $primeira);
        $this->assertNotSame($primeira, $segunda);
    }

    public function test_error_pages_also_receive_the_headers(): void
    {
        $this->get('/pagina-inexistente')
            ->assertNotFound()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_csp_allows_turnstile_only_when_it_is_configured(): void
    {
        config(['services.turnstile.site_key' => 'chave', 'services.turnstile.secret_key' => 'segredo']);
        $demanda = Demanda::factory()->create();

        $csp = $this->get("/demandas/{$demanda->id_demanda}")->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('script-src \'self\'', $csp);
        $this->assertStringContainsString(Turnstile::ORIGEM, $csp);
        $this->assertStringContainsString('frame-src '.Turnstile::ORIGEM, $csp);
    }

    public function test_csp_allows_the_vite_dev_server_during_development(): void
    {
        File::put($this->hotFile, 'http://localhost:5173');

        $csp = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('script-src \'self\'', $csp);
        $this->assertStringContainsString('http://localhost:5173', $csp);
        $this->assertStringContainsString('connect-src \'self\' http://localhost:5173 ws://localhost:5173', $csp);
        // Imagens importadas nos componentes (ex.: logo do cabeçalho) também vêm do Vite.
        $this->assertStringContainsString('img-src \'self\' data: http://localhost:5173', $csp);
    }

    public function test_ipv6_dev_server_falls_back_to_scheme_sources(): void
    {
        File::put($this->hotFile, 'http://[::1]:5173');

        $csp = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertStringNotContainsString('[::1]', $csp);
        $this->assertStringContainsString('connect-src \'self\' http: ws:', $csp);
    }

    public function test_report_only_mode_does_not_block(): void
    {
        config(['seguranca.csp.somente_relatorio' => true]);

        $this->get('/')
            ->assertHeaderMissing('Content-Security-Policy')
            ->assertHeader('Content-Security-Policy-Report-Only');
    }

    public function test_security_check_command_runs(): void
    {
        $this->artisan('seguranca:verificar')
            ->expectsOutputToContain('APP_DEBUG=false')
            ->assertSuccessful();
    }
}
