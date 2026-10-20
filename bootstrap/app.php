<?php

use App\Http\Middleware\CabecalhosSeguranca;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CSP, anti-clickjacking, nosniff e HSTS em todas as respostas.
        $middleware->append(CabecalhosSeguranca::class);

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
    ->withExceptions(function (Exceptions $exceptions): void {
        // RNF12: respostas JSON nunca expõem mensagens cruas do PHP/MySQL.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->expectsJson() || $e instanceof ValidationException) {
                return null; // HTML usa resources/views/errors/*; validação segue o padrão.
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            if ($status === 500 && config('app.debug')) {
                return null; // Em desenvolvimento, mantém o detalhe para depuração.
            }

            $mensagem = match ($status) {
                403 => $e->getMessage() ?: 'Você não tem permissão para realizar esta ação.',
                404 => 'O registro solicitado não foi encontrado.',
                419 => 'Sua sessão expirou. Recarregue a página e tente novamente.',
                429 => 'Muitas tentativas em pouco tempo. Aguarde um minuto e tente novamente.',
                default => 'Ocorreu um erro ao processar sua solicitação. Tente novamente.',
            };

            return response()->json(['message' => $mensagem], $status);
        });
    })->create();
