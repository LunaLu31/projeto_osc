<?php

namespace App\Providers;

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

        $this->configurarLimites();
        $this->configurarDocumentacaoApi();
    }

    private function configurarLimites(): void
    {
        // Formulário público sem login: limita envios por IP (RF05).
        RateLimiter::for('manifestacoes', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        // API: 60 requisições por minuto por usuário do token (OSC ou admin) ou, sem token, por IP.
        // Usa o guard "sanctum" porque este limite roda antes do middleware auth:sanctum.
        RateLimiter::for('api', function (Request $request) {
            $usuario = $request->user('sanctum');

            return Limit::perMinute(60)->by(
                $usuario ? $usuario->getMorphClass().':'.$usuario->getAuthIdentifier() : 'ip:'.$request->ip()
            );
        });
    }

    private function configurarDocumentacaoApi(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')->setDescription(
                        'Token obtido em `POST /api/v1/osc/tokens` ou `POST /api/v1/admin/tokens`.'
                    )
                );
            })
            // Respostas que vêm de middlewares (abilities do token e limite de requisições)
            // e por isso não são inferidas a partir do código dos controllers.
            ->withOperationTransformers(function (Operation $operation) {
                $codigos = collect($operation->responses)
                    ->map(fn ($resposta) => $resposta instanceof Response ? (int) $resposta->code : null)
                    ->filter();

                $protegida = $operation->security !== [];

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
