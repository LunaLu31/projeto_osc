<?php

namespace App\Console\Commands;

use App\Support\Turnstile;
use Illuminate\Console\Command;

/**
 * Lista de verificação de segurança antes do deploy.
 * Uso: php artisan seguranca:verificar
 */
class VerificarSeguranca extends Command
{
    protected $signature = 'seguranca:verificar';

    protected $description = 'Confere se a configuração do ambiente segue as práticas de segurança para produção';

    public function handle(): int
    {
        $producao = app()->isProduction();
        $dbUsuario = (string) config('database.connections.'.config('database.default').'.username');
        $dbSenha = (string) config('database.connections.'.config('database.default').'.password');

        // [descrição, passou?, crítico em produção?]
        $verificacoes = [
            ['APP_ENV=production', $producao, true],
            ['APP_DEBUG=false (sem erros crus para o usuário – RNF12)', ! config('app.debug'), true],
            ['APP_KEY definida', filled(config('app.key')), true],
            ['APP_URL usa HTTPS', str_starts_with((string) config('app.url'), 'https://'), true],
            ['SESSION_SECURE_COOKIE=true (cookies de sessão e de login só via HTTPS)', (bool) config('session.secure'), true],
            ['SESSION_ENCRYPT=true (sessão criptografada)', (bool) config('session.encrypt'), false],
            ['SESSION_HTTP_ONLY=true (cookie inacessível ao JavaScript)', (bool) config('session.http_only'), true],
            ['Usuário do banco dedicado (não root)', ! in_array(strtolower($dbUsuario), ['root', 'admin', ''], true), true],
            ['Senha do banco com 16+ caracteres', mb_strlen($dbSenha) >= 16, true],
            ['Content Security Policy ativa e bloqueando', config('seguranca.csp.ativa') && ! config('seguranca.csp.somente_relatorio'), true],
            ['Turnstile configurado (anti-robô no formulário público)', Turnstile::ativo(), false],
            ['Assets compilados (npm run build)', is_file(public_path('build/manifest.json')), true],
            ['Servidor do Vite desligado (sem arquivo public/hot)', ! is_file(public_path('hot')), true],
            ['CORS da API restrito a origens conhecidas (CORS_ALLOWED_ORIGINS sem *)', ! in_array('*', (array) config('cors.allowed_origins'), true), false],
            ['JWT_SECRET com 32+ caracteres (chave própria dos tokens de login)', mb_strlen((string) config('jwt.segredo')) >= 32, false],
            ['Tokens de login com expiração (JWT_TTL)', (int) config('jwt.validade_minutos') > 0, true],
            ['Documentação da API não pública (API_DOCS_PUBLIC=false)', ! config('seguranca.docs_api_publica'), false],
        ];

        $falhasCriticas = 0;
        $linhas = [];

        foreach ($verificacoes as [$descricao, $ok, $critico]) {
            $situacao = $ok ? '<fg=green>OK</>' : ($critico ? '<fg=red>FALHA</>' : '<fg=yellow>ATENÇÃO</>');
            $falhasCriticas += (! $ok && $critico) ? 1 : 0;
            $linhas[] = [$situacao, $descricao];
        }

        $this->table(['Situação', 'Verificação'], $linhas);

        if ($falhasCriticas === 0) {
            $this->info('Configuração pronta para produção.');

            return self::SUCCESS;
        }

        $mensagem = "{$falhasCriticas} verificação(ões) crítica(s) pendente(s).";

        if ($producao) {
            $this->error($mensagem);

            return self::FAILURE;
        }

        $this->warn($mensagem.' Esperado em desenvolvimento; corrija antes do deploy.');

        return self::SUCCESS;
    }
}
