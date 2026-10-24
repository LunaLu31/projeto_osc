# Redcam · UOSCC

Plataforma que conecta organizações da sociedade civil (OSCs) de Camaçari a pessoas que querem ajudar.
As OSCs publicam necessidades reais (demandas); apoiadores encontram, filtram e manifestam interesse
sem precisar criar conta; a UOSCC administra a rede.

> Projeto de TCC — MVP (Versão A). Grupo: Gabriel Rodrigues, Engenilson, Mariana, Luna e Lauane.

## Stack

| Camada | Tecnologias |
|---|---|
| Back-end | PHP 8.2+, Laravel 12, Eloquent, JWT (firebase/php-jwt, site e API), Scramble (OpenAPI) |
| Front-end | React 19 + TypeScript via Inertia.js (SSR), React Compiler, Ziggy (rotas tipadas), Tailwind CSS 4 + design system próprio, tema claro/escuro |
| Banco | MySQL 8 (`database/schema/uoscc_redecam.sql` + migrations equivalentes) |
| Build e qualidade | Vite 7, ESLint, Prettier, Vitest + Testing Library + axe-core, PHPUnit, Laravel Pint |

## Arquitetura

```
app/
├── Http/Controllers/
│   ├── Publico/  Osc/  Admin/      site (retornam páginas React via Inertia::render)
│   └── Api/V1/                     API REST JSON (/api/v1)
├── Http/Requests/                  validação compartilhada entre site e API
├── Http/Resources/V1/              formato dos dados (API e props das páginas) – controle LGPD
├── Http/Middleware/                Inertia, cabeçalhos de segurança/CSP, JSON na API
├── Models/  Policies/  Rules/
└── Services/                       regras de negócio (métricas, gestão de OSCs)

resources/js/
├── pages/        uma página React por tela (Publico/, Osc/, Admin/, Erro.tsx)
├── layouts/      LayoutSite, LayoutOsc, LayoutAdmin, LayoutAutenticacao
├── components/   componentes reutilizáveis do design system
├── hooks/        hooks (ex.: useRecarregandoPagina para skeletons)
├── lib/          utilitários (formatação, classes, ações, props tipadas)
├── test/         configuração dos testes de componentes
└── types/        api.d.ts (gerado do OpenAPI), ziggy.d.ts (gerado) e tipos do domínio

resources/css/    tokens (app.css), temas (theme.css) e componentes do design system
```

### Front-end: práticas adotadas

- **Tipos gerados**: `types/api.d.ts` vem do OpenAPI (`npm run tipos:api`); os tipos do domínio derivam
  dele. Uma mudança num Resource do Laravel quebra a checagem de tipos onde for preciso ajustar.
- **Estados de requisição**: o Inertia é o cliente HTTP (login por JWT em cookie e CSRF da sessão). Erros de validação
  aparecem em cada campo; recargas da página mostram *skeletons*; falhas de rede ou respostas
  inesperadas exibem um aviso global (`AvisoConexao`), sem expor erros técnicos.
- **Responsivo e mobile first**: a estrutura das páginas usa utilitários do Tailwind (`sm:`, `md:`…);
  a aparência dos componentes fica em classes do design system (`@layer components`).
- **Tema claro/escuro**: tokens com `light-dark()`; a preferência fica no cookie `tema` e é aplicada
  já no HTML do servidor, sem piscar. Botão de alternância em todos os layouts.
- **Acessibilidade**: WCAG 2.1 AA (contraste ≥ 4,5:1 nos dois temas), diálogo de confirmação nativo
  (`<dialog>`), foco gerenciado e checagem automática com axe-core nos testes.
- **Desempenho**: React Compiler (memorização automática), páginas em chunks separados e listas
  paginadas no servidor.

- **Site**: rotas e autorização ficam no Laravel; cada controller entrega uma página React com props
  tipadas. Navegação e formulários sem recarregar a página, com CSRF do Laravel.
- **Login por JWT** (`app/Auth`, `config/jwt.php`): token HS256 assinado, emitido por perfil.
  - Site: em cookie `HttpOnly` (inacessível ao JavaScript). Com **"Manter conectado"** vale 7 dias e
    é renovado enquanto a pessoa usa o sistema, então só expira após 7 dias sem acesso; sem a opção,
    termina ao fechar o navegador (máx. 2 h).
  - API: no cabeçalho `Authorization: Bearer`, válido por 7 dias (`POST …/tokens/renovar` troca por um novo).
  - Rotas protegidas: `auth:osc`/`auth:admin` no site e `jwt:osc`/`jwt:admin` na API (401 sem token
    válido, 403 com token de outro perfil). Sair revoga o token; trocar a senha invalida todos.
- **API REST** (`/api/v1`): JWT com escopo por perfil (`osc`/`admin`).
  Documentação Swagger em **`/docs/api`** (OpenAPI 3.1 gerado do código; cópia em `docs/openapi.json`).
- **Perfis**: apoiador (sem login), OSC (guard `osc`) e administrador (guard `admin`).
- **SEO**: cada página define título, descrição e tags Open Graph (`<Head>` do Inertia).

## Como rodar

```bash
cp .env.example .env            # ajuste DB_* e APP_URL
composer install && npm install
php artisan key:generate
php artisan migrate --seed      # cria todas as tabelas (mesmo esquema do script físico) e o admin padrão

composer dev                    # servidor Laravel + fila + logs + Vite
```

O script `database/schema/uoscc_redecam.sql` documenta o modelo físico do banco (entregável do TCC);
as migrations geram exatamente as mesmas tabelas, além das tabelas internas do Laravel
(sessões, cache e filas).

Acesso de desenvolvimento: `admin@uoscc.org` / `admin123` em `/admin` (troque em produção).

### Renderização no servidor (SSR)

Em produção as páginas são renderizadas no servidor (HTML pronto para buscadores e para as prévias
de links compartilhados), e o React "hidrata" esse HTML no navegador:

```bash
npm run build:ssr               # build do cliente + bundle de SSR (bootstrap/ssr)
php artisan inertia:start-ssr   # servidor Node de SSR (porta 13714); use um supervisor em produção
# .env: INERTIA_SSR_ENABLED=true
```

Sem o servidor de SSR o site continua funcionando, renderizado no navegador. Para testar localmente
com SSR: `composer dev:ssr`.
Dados fictícios: `php artisan migrate:fresh --seed --seeder=DemonstracaoSeeder` (apaga o banco e cria 2 admins,
`admin@uoscc.org` com 10 OSCs e `coordenacao@uoscc.org` com 15, ambos com senha `admin123`; OSCs com senha `senha-segura`).

## Qualidade

```bash
php artisan test        # testes do back-end (site com assertInertia + API), banco MySQL uoscc_redecam_test
npm test                # testes de componentes React (Vitest + Testing Library + axe-core)
npm run verificar       # TypeScript + ESLint + Prettier + tipos da API atualizados
vendor/bin/pint         # padrão de código PHP (PSR-12)
npm run rotas:tipos     # regenera os tipos das rotas após alterar routes/web.php
php artisan scramble:export && npm run tipos:api   # atualiza o OpenAPI e os tipos do front após alterar a API
```

## Segurança

CSP com nonce e sem estilos/scripts inline, HSTS, `X-Frame-Options`, senhas com bcrypt e política
mínima, limite de tentativas de login (e-mail + IP), anti-spam no formulário público (honeypot,
armadilha de tempo e Cloudflare Turnstile opcional), login por JWT com expiração, revogação e cookie
`HttpOnly`/`SameSite=Lax`, e contatos de
apoiadores visíveis apenas para a OSC responsável (LGPD).

Antes do deploy: `php artisan seguranca:verificar`.
