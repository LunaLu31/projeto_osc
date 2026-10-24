<?php

namespace Tests\Feature;

use App\Auth\JwtGuard;
use App\Models\Adm;
use App\Models\Osc;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

/**
 * Login do site por JWT em cookie HttpOnly: voltar às páginas sem entrar de novo,
 * "Manter conectado", renovação com o uso, expiração, saída e troca de senha.
 * Cada requisição leva só o cookie de login (como o navegador faria dias depois).
 */
class LoginJwtTest extends TestCase
{
    use RefreshDatabase;

    private const COOKIE_OSC = JwtGuard::COOKIES['osc'];

    private Osc $osc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->osc = Osc::factory()->create();
    }

    public function test_login_sets_a_signed_jwt_in_an_http_only_cookie(): void
    {
        $cookie = $this->entrar(lembrar: true);

        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
        $this->assertCount(3, explode('.', $cookie->getValue()));

        $claims = json_decode(JWT::urlsafeB64Decode(explode('.', $cookie->getValue())[1]), true);
        $this->assertSame('osc', $claims['perfil']);
        $this->assertSame((string) $this->osc->id_osc, $claims['sub']);
        $this->assertTrue($claims['lembrar']);
    }

    public function test_keep_me_signed_in_lasts_seven_days_even_after_closing_the_browser(): void
    {
        $cookie = $this->entrar(lembrar: true);

        // Cookie persistente: continua no navegador depois de fechado.
        $this->assertEqualsWithDelta(now()->addMinutes(config('jwt.validade_minutos'))->timestamp, $cookie->getExpiresTime(), 5);

        $this->travel(6)->days();
        $this->comCookie($cookie)->get('/osc/painel')->assertOk();
    }

    public function test_without_keep_me_signed_in_the_cookie_ends_with_the_browser_and_lasts_two_hours(): void
    {
        $cookie = $this->entrar(lembrar: false);

        $this->assertSame(0, $cookie->getExpiresTime());

        $this->travel(config('jwt.validade_sem_lembrar_minutos') + 1)->minutes();
        $this->comCookie($cookie)->get('/osc/painel')->assertRedirect('/osc/login');
    }

    public function test_using_the_system_renews_the_login_so_it_only_expires_after_seven_days_without_access(): void
    {
        $cookie = $this->entrar(lembrar: true);

        // Volta a cada 5 dias: cada visita renova o token, e o login nunca expira.
        for ($visita = 0; $visita < 3; $visita++) {
            $this->travel(5)->days();
            $resposta = $this->comCookie($cookie)->get('/osc/painel')->assertOk();
            $cookie = $this->cookieDe($resposta);
        }

        // Mais de 7 dias sem acesso: precisa entrar de novo.
        $this->travel(8)->days();
        $this->comCookie($cookie)->get('/osc/painel')->assertRedirect('/osc/login');
    }

    public function test_a_recent_token_is_not_reissued_on_every_request(): void
    {
        $cookie = $this->entrar(lembrar: true);

        $this->travel(10)->minutes();

        $this->assertNull($this->comCookie($cookie)->get('/osc/painel')->assertOk()->getCookie(self::COOKIE_OSC, false));
    }

    public function test_logged_in_users_skip_the_login_page(): void
    {
        $cookie = $this->entrar(lembrar: true);

        $this->comCookie($cookie)->get('/osc/login')->assertRedirect('/osc/painel');
    }

    public function test_logout_deletes_the_cookie_and_revokes_the_token(): void
    {
        $cookie = $this->entrar(lembrar: true);

        $resposta = $this->comCookie($cookie)->post('/osc/logout')->assertRedirect('/');
        $this->assertTrue($this->cookieDe($resposta)->isCleared());

        // Uma cópia guardada do token também deixa de valer.
        $this->comCookie($cookie)->get('/osc/painel')->assertRedirect('/osc/login');
    }

    public function test_changing_the_password_logs_out_other_devices_and_keeps_this_one(): void
    {
        $outroDispositivo = $this->entrar(lembrar: true);
        $esteDispositivo = $this->entrar(lembrar: true);

        $resposta = $this->comCookie($esteDispositivo)->put('/osc/senha', [
            'senha_atual' => 'senha-segura',
            'password' => 'novaSenha2026',
            'password_confirmation' => 'novaSenha2026',
        ])->assertRedirect('/osc/painel');
        $novo = $this->cookieDe($resposta);

        $this->comCookie($outroDispositivo)->get('/osc/painel')->assertRedirect('/osc/login');
        $this->comCookie($esteDispositivo)->get('/osc/painel')->assertRedirect('/osc/login');
        $this->comCookie($novo)->get('/osc/painel')->assertOk();
        $this->assertGreaterThan(0, $novo->getExpiresTime(), 'mantém o "Manter conectado"');
    }

    public function test_a_tampered_cookie_does_not_authenticate(): void
    {
        $cookie = $this->entrar(lembrar: true);
        [$cabecalho, $payload, $assinatura] = explode('.', $cookie->getValue());
        $claims = json_decode(JWT::urlsafeB64Decode($payload), true);
        $outraOsc = Osc::factory()->create();
        $adulterado = $cabecalho.'.'.JWT::urlsafeB64Encode(json_encode(['sub' => (string) $outraOsc->id_osc] + $claims)).'.'.$assinatura;

        $this->forgetGuards();
        $this->withUnencryptedCookie(self::COOKIE_OSC, $adulterado)->get('/osc/painel')->assertRedirect('/osc/login');
    }

    public function test_osc_and_admin_logins_are_separate(): void
    {
        $admin = Adm::factory()->create();
        $resposta = $this->post('/admin/login', ['email' => $admin->email, 'password' => 'senha-segura', 'lembrar' => true]);
        $cookieAdmin = $resposta->getCookie(JwtGuard::COOKIES['admin'], false);
        $this->assertNotNull($cookieAdmin);

        // O token do admin não abre a área da OSC, nem copiado para o cookie dela.
        $this->forgetGuards();
        $this->withUnencryptedCookie(self::COOKIE_OSC, $cookieAdmin->getValue())->get('/osc/painel')->assertRedirect('/osc/login');

        $this->forgetGuards();
        $this->withUnencryptedCookie(JwtGuard::COOKIES['admin'], $cookieAdmin->getValue())->get('/admin')->assertOk();
    }

    private function entrar(bool $lembrar): Cookie
    {
        $this->forgetGuards();
        $resposta = $this->post('/osc/login', [
            'email' => $this->osc->email,
            'password' => 'senha-segura',
            'lembrar' => $lembrar,
        ])->assertRedirect('/osc/painel');

        return $this->cookieDe($resposta);
    }

    private function cookieDe(TestResponse $resposta): Cookie
    {
        $cookie = $resposta->getCookie(self::COOKIE_OSC, false);
        $this->assertNotNull($cookie, 'a resposta deveria trazer o cookie de login');

        return $cookie;
    }

    /** Nova requisição só com o cookie de login: nenhum estado guardado no servidor entre elas. */
    private function comCookie(Cookie $cookie): static
    {
        $this->forgetGuards();
        $this->flushSession();

        return $this->withUnencryptedCookie(self::COOKIE_OSC, $cookie->getValue());
    }

    /**
     * Esquece o estado da requisição anterior que, nos testes, sobrevive no mesmo container:
     * o usuário dos guards e a fila de cookies (que o Laravel reenviaria em toda resposta).
     */
    private function forgetGuards(): void
    {
        $this->app['auth']->forgetGuards();
        $this->app['cookie']->flushQueuedCookies();
    }
}
