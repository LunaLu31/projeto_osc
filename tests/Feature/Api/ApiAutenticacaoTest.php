<?php

namespace Tests\Feature\Api;

use App\Models\Adm;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fluxo real de tokens Bearer (sem Sanctum::actingAs), incluindo revogação e expiração.
 */
class ApiAutenticacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_osc_gets_a_token_and_uses_it(): void
    {
        $osc = Osc::factory()->create(['nome' => 'Organização Exemplo']);

        $resposta = $this->postJson('/api/v1/osc/tokens', [
            'email' => $osc->email,
            'password' => 'senha-segura',
            'dispositivo' => 'app-android',
        ])->assertCreated()
            ->assertJsonPath('tipo', 'Bearer')
            ->assertJsonPath('osc.iniciais', 'OE')
            ->assertJsonStructure(['token', 'expira_em', 'osc' => ['id', 'nome', 'email']]);

        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'app-android', 'tokenable_id' => $osc->id_osc]);

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

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_osc_credentials_do_not_work_on_the_admin_login(): void
    {
        $osc = Osc::factory()->create();

        $this->postJson('/api/v1/admin/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])
            ->assertUnprocessable();
    }

    public function test_requests_without_token_get_401_in_portuguese(): void
    {
        $this->getJson('/api/v1/osc/perfil')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Autenticação necessária. Envie um token válido no cabeçalho Authorization: Bearer {token}.');

        $this->withToken('token-invalido')->getJson('/api/v1/admin/painel')->assertUnauthorized();
    }

    public function test_tokens_are_restricted_to_their_profile(): void
    {
        $tokenOsc = Osc::factory()->create()->createToken('teste', ['osc'])->plainTextToken;
        $tokenAdmin = Adm::factory()->create()->createToken('teste', ['admin'])->plainTextToken;

        $this->withToken($tokenOsc)->getJson('/api/v1/admin/painel')
            ->assertForbidden()
            ->assertJsonPath('message', 'Você não tem permissão para realizar esta ação.');

        $this->app['auth']->forgetGuards();

        $this->withToken($tokenAdmin)->getJson('/api/v1/osc/painel')->assertForbidden();
    }

    public function test_revoked_token_stops_working(): void
    {
        $osc = Osc::factory()->create();
        $token = $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])->json('token');

        $this->withToken($token)->deleteJson('/api/v1/osc/tokens/atual')->assertNoContent();
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/v1/osc/perfil')->assertUnauthorized();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_tokens_expire(): void
    {
        $osc = Osc::factory()->create();
        $token = $this->postJson('/api/v1/osc/tokens', ['email' => $osc->email, 'password' => 'senha-segura'])->json('token');

        $this->travel(config('sanctum.expiration') + 1)->minutes();

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
}
