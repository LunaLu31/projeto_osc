<?php

use App\Http\Middleware\CabecalhosSeguranca;
use App\Http\Middleware\ForcarRespostaJson;
use App\Http\Middleware\HandleInertiaRequests;
use App\Support\Tema;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

$respondeEmJson = fn (Request $request) => $request->is('api/*') || $request->expectsJson();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CSP, anti-clickjacking, nosniff e HSTS em todas as respostas.
        $middleware->append(CabecalhosSeguranca::class);

        // Preferência de tema: escrita pelo JavaScript e sem dado sensível (não criptografada).
        $middleware->encryptCookies(except: [Tema::COOKIE]);

        // Site: páginas React via Inertia (props compartilhadas em HandleInertiaRequests).
        $middleware->web(append: [HandleInertiaRequests::class]);

        // API: sempre JSON e limite de requisições (RateLimiter "api" no AppServiceProvider).
        $middleware->api(prepend: [ForcarRespostaJson::class]);
        $middleware->throttleApi();

        // Escopos dos tokens do Sanctum: "osc" ou "admin".
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        $areaAdmin = fn (Request $request) => $request->is('admin', 'admin/*');

        // Visitante em rota protegida vai para o login da área correspondente.
        $middleware->redirectGuestsTo(fn (Request $request) => $areaAdmin($request)
            ? route('admin.login')
            : route('osc.login'));

        // Usuário já autenticado que abre a tela de login vai para o seu painel.
        $middleware->redirectUsersTo(fn (Request $request) => $areaAdmin($request)
            ? route('admin.painel')
            : route('osc.painel'));
    })
    ->withExceptions(function (Exceptions $exceptions) use ($respondeEmJson): void {
        $exceptions->shouldRenderJsonWhen($respondeEmJson);

        // RNF12: respostas JSON nunca expõem mensagens cruas do PHP/MySQL.
        $exceptions->render(function (Throwable $e, Request $request) use ($respondeEmJson) {
            if (! $respondeEmJson($request) || $e instanceof ValidationException) {
                return null; // HTML usa resources/views/errors/*; validação segue o padrão (422).
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Autenticação necessária. Envie um token válido no cabeçalho Authorization: Bearer {token}.',
                ], 401);
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            if ($status === 500 && config('app.debug')) {
                return null; // Em desenvolvimento, mantém o detalhe para depuração.
            }

            $mensagem = match ($status) {
                // Negações de policy/ability chegam em inglês; mensagens de abort() já estão em português.
                403 => ($e->getPrevious() instanceof AuthorizationException || $e->getMessage() === '')
                    ? 'Você não tem permissão para realizar esta ação.'
                    : $e->getMessage(),
                404 => 'O registro solicitado não foi encontrado.',
                405 => 'Método HTTP não permitido para este endereço.',
                419 => 'Sua sessão expirou. Recarregue a página e tente novamente.',
                429 => 'Muitas tentativas em pouco tempo. Aguarde um minuto e tente novamente.',
                default => 'Ocorreu um erro ao processar sua solicitação. Tente novamente.',
            };

            // Mantém cabeçalhos úteis, como Retry-After (429) e Allow (405).
            $headers = $e instanceof HttpExceptionInterface ? $e->getHeaders() : [];

            return response()->json(['message' => $mensagem], $status, $headers);
        });

        // Site (Inertia): erros viram uma página React amigável, sem detalhes técnicos (RNF12).
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) use ($respondeEmJson) {
            $status = $response->getStatusCode();

            if ($respondeEmJson($request)) {
                return $response;
            }

            if ($status === 419) {
                return back()->with('erro', 'Sua sessão expirou. Tente novamente.');
            }

            // Em desenvolvimento, erros 500 mantêm a página de depuração do Laravel.
            $mostrarDepuracao = $status >= 500 && config('app.debug');

            if (in_array($status, [403, 404, 429, 500, 503], true) && ! $mostrarDepuracao) {
                return Inertia::render('Erro', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            return $response;
        });
    })->create();
