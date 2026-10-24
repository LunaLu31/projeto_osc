<?php

namespace App\Providers;

use App\Auth\JwtGuard;
use App\Auth\TokensJwt;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TokensJwt::class);

        // A documentação usa Swagger UI servido pelo próprio projeto (rotas em routes/web.php).
        Scramble::ignoreDefaultRoutes();
    }

    public function boot(): void
    {
        // Produção: links sempre em HTTPS e bloqueio de migrate:fresh/db:wipe acidentais.
        URL::forceHttps($this->app->isProduction());
        DB::prohibitDestructiveCommands($this->app->isProduction());

        // Em desenvolvimento, acusa consultas N+1 e atributos inexistentes (RNF07).
        Model::shouldBeStrict(! $this->app->isProduction());

        // Política de senha de OSCs e administradores (RNF05).
        Password::defaults(fn () => Password::min(8)->letters()->numbers());

        $this->configurarLoginJwt();
        $this->configurarLimites();
        $this->configurarDocumentacaoApi();
    }

    /** Driver "jwt" dos guards de config/auth.php (site em cookie, API em Bearer). */
    private function configurarLoginJwt(): void
    {
        Auth::extend('jwt', function ($app, string $nome, array $config) {
            $guard = new JwtGuard(
                $app->make(TokensJwt::class),
                Auth::createUserProvider($config['provider']),
                $app['request'],
                $config['perfil'],
                $config['origem'],
            );

            // A cada nova requisição o guard volta a ler o token dela.
            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    private function configurarLimites(): void
    {
        // Formulário público sem login: limita envios por IP (RF05).
        RateLimiter::for('manifestacoes', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        // API: 60 requisições por minuto por usuário do token (OSC ou admin) ou, sem token, por IP.
        // Lê o token direto porque este limite roda antes do middleware jwt:{perfil}.
        RateLimiter::for('api', function (Request $request) {
            $claims = $this->app->make(TokensJwt::class)->ler($request->bearerToken());

            return Limit::perMinute(60)->by($claims ? "{$claims->perfil}:{$claims->sub}" : 'ip:'.$request->ip());
        });
    }

    private function configurarDocumentacaoApi(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer', 'JWT')->setDescription(
                        'JWT obtido em `POST /api/v1/osc/tokens` ou `POST /api/v1/admin/tokens`, válido por 7 dias '
                        .'(renove em `POST /api/v1/{perfil}/tokens/renovar`).'
                    )
                );
            })
            // Respostas que vêm de middlewares (jwt:{perfil} e limite de requisições)
            // e por isso não são inferidas a partir do código dos controllers.
            ->withOperationTransformers(function (Operation $operation) {
                $codigos = collect($operation->responses)
                    ->map(fn ($resposta) => $resposta instanceof Response ? (int) $resposta->code : null)
                    ->filter();

                $protegida = $operation->security !== [];

                if ($protegida && ! $codigos->contains(401)) {
                    $operation->addResponse($this->respostaDeErro(401,
                        'Token ausente, inválido, expirado ou revogado (saída ou troca de senha).'));
                }

                if ($protegida && ! $codigos->contains(403)) {
                    $operation->addResponse($this->respostaDeErro(403,
                        'Token de outro perfil (ex.: token de OSC em rota de administração).'));
                }

                if (! $codigos->contains(429)) {
                    $operation->addResponse($this->respostaDeErro(429,
                        'Limite de requisições excedido. Aguarde o tempo indicado no cabeçalho `Retry-After`.'));
                }
            });

        // Fora do ambiente local, a documentação só é exibida com API_DOCS_PUBLIC=true.
        Gate::define('viewApiDocs', fn ($usuario = null) => (bool) config('seguranca.docs_api_publica'));
    }

    private function respostaDeErro(int $codigo, string $descricao): Response
    {
        $corpo = (new ObjectType)->addProperty('message', new StringType)->setRequired(['message']);

        return Response::make($codigo)
            ->setDescription($descricao)
            ->setContent('application/json', Schema::fromType($corpo));
    }
}
