<?php

namespace Tests\Feature\Api;

use App\Models\Adm;
use App\Models\Osc;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fluxo real dos JWT da API (sem actingAs): emissão, uso, perfil, revogação, renovação,
 * expiração e tokens adulterados.
 */
class ApiAutenticacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_osc_gets_a_jwt_and_uses_it(): void
    {
        $osc = Osc::factory()->create(['nome' => 'Organização Exemplo']);

        $resposta = $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertCreated()
            ->assertJsonPath('tipo', 'Bearer')
            ->assertJsonPath('osc.iniciais', 'OE')
            ->assertJsonStructure(['token', 'expira_em', 'osc' => ['id', 'nome', 'email']]);

        // Um JWT de verdade: cabeçalho, claims e assinatura; vale 7 dias.
        $claims = $this->claims($resposta->json('token'));
        $this->assertSame('osc', $claims['perfil']);
        $this->assertSame((string) $osc->id_osc, $claims['sub']);
        $this->assertSame(config('jwt.validade_minutos') * 60, $claims['exp'] - $claims['iat']);
        $this->assertArrayNotHasKey('senha_hash', $claims);

        $this->withToken($resposta->json('token'))
            ->getJson('/api/v1/osc/perfil')
            ->assertOk()
            ->assertJsonPath('data.id', $osc->id_osc)
            ->assertJsonMissingPath('data.senha_hash');
    }

    public function test_invalid_credentials_and_lockout(): void
    {
        $osc = Osc::factory()->create();

        $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'errada'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'E-mail ou senha inválidos.']);

        for ($i = 0; $i < 4; $i++) {
            $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'errada']);
        }

        $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_osc_credentials_do_not_work_on_the_admin_login(): void
    {
        $osc = Osc::factory()->create();

        $this->postJson('/api/v1/admin/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertUnprocessable();
    }

    public function test_requests_without_a_valid_token_get_401_in_portuguese(): void
    {
        $this->getJson('/api/v1/osc/perfil')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Autenticação necessária. Envie um token válido no cabeçalho Authorization: Bearer {token}.');

        $this->withToken('token-invalido')->getJson('/api/v1/admin/painel')->assertUnauthorized();
    }

    public function test_tokens_are_restricted_to_their_profile(): void
    {
        $tokenOsc = $this->tokenJwt(Osc::factory()->create(), 'osc');
        $tokenAdmin = $this->tokenJwt(Adm::factory()->create(), 'admin');

        $this->withToken($tokenOsc)->getJson('/api/v1/admin/painel')
            ->assertForbidden()
            ->assertJsonPath('message', 'Você não tem permissão para realizar esta ação.');

        $this->app['auth']->forgetGuards();

        $this->withToken($tokenAdmin)->getJson('/api/v1/osc/painel')->assertForbidden();
    }

    public function test_a_tampered_token_or_one_signed_with_another_key_is_rejected(): void
    {
        $osc = Osc::factory()->create();
        $token = $this->tokenJwt($osc, 'osc');

        // Trocar o perfil no payload sem reassinar quebra a assinatura.
        [$cabecalho, , $assinatura] = explode('.', $token);
        $payload = JWT::urlsafeB64Encode(json_encode(['perfil' => 'admin'] + $this->claims($token)));
        $this->withToken("{$cabecalho}.{$payload}.{$assinatura}")->getJson('/api/v1/admin/painel')->assertUnauthorized();

        // Mesmos claims, assinados com outra chave.
        $forjado = JWT::encode($this->claims($token), str_repeat('x', 32), 'HS256');
        $this->app['auth']->forgetGuards();
        $this->withToken($forjado)->getJson('/api/v1/osc/perfil')->assertUnauthorized();
    }

    public function test_logout_revokes_the_token(): void
    {
        $osc = Osc::factory()->create();
        $token = $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])->json('token');

        $this->withToken($token)->deleteJson('/api/v1/osc/tokens/atual')->assertNoContent();
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/v1/osc/perfil')->assertUnauthorized();
    }

    public function test_renewing_returns_a_new_token_and_revokes_the_old_one(): void
    {
        $osc = Osc::factory()->create();
        $antigo = $this->tokenJwt($osc, 'osc');

        $this->travel(3)->days();
        $novo = $this->withToken($antigo)->postJson('/api/v1/osc/tokens/renovar')
            ->assertOk()
            ->assertJsonPath('tipo', 'Bearer')
            ->json('token');

        $this->assertGreaterThan($this->claims($antigo)['exp'], $this->claims($novo)['exp']);

        $this->app['auth']->forgetGuards();
        $this->withToken($antigo)->getJson('/api/v1/osc/perfil')->assertUnauthorized();
        $this->app['auth']->forgetGuards();
        $this->withToken($novo)->getJson('/api/v1/osc/perfil')->assertOk();
    }

    public function test_tokens_expire_after_seven_days(): void
    {
        $osc = Osc::factory()->create();
        $token = $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])->json('token');

        $this->travel(config('jwt.validade_minutos') - 1)->minutes();
        $this->withToken($token)->getJson('/api/v1/osc/perfil')->assertOk();

        $this->travel(2)->minutes();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/osc/perfil')->assertUnauthorized();
    }

    public function test_admin_gets_a_token(): void
    {
        $admin = Adm::factory()->create();

        $token = $this->postJson('/api/v1/admin/tokens', ['email' => $admin->email, 'password' => 'senha-segura'])
            ->assertCreated()
            ->assertJsonPath('admin.id', $admin->id_admin)
            ->json('token');

        $this->withToken($token)->getJson('/api/v1/admin/painel')
            ->assertOk()
            ->assertJsonPath('admin.email', $admin->email);
    }

    /** Claims de um JWT, lidos sem validar (só para inspecionar nos testes). */
    private function claims(string $token): array
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $token)[1]), true);
    }
}
