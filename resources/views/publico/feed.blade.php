{{-- Página inicial: apresentação, métricas e listagem pública de demandas (RF04). --}}
<x-layouts.site>
    <section class="hero">
        <div class="hero__content">
            <div class="hero__eyebrow">
                <span><x-icon name="location" :size="15" /> Camaçari, Bahia</span>
                <span>Conectando quem transforma</span>
            </div>
            <h1>Sua vontade de ajudar encontra <em>quem precisa.</em></h1>
            <p>Descubra necessidades reais de organizações sociais de Camaçari e contribua com doações, tempo ou conhecimento. Sem burocracia.</p>
            <div class="hero__actions">
                <a href="#demandas" class="button button--primary">Encontrar uma causa <x-icon name="arrow" /></a>
                <a href="#como-funciona" class="button button--light">Como funciona</a>
            </div>
            @if ($totalOscs > 0)
                <div class="hero__trust">
                    <span class="avatar-stack" aria-hidden="true">
                        @foreach ($iniciaisOscsDestaque as $iniciais)
                            <i>{{ $iniciais }}</i>
                        @endforeach
                    </span>
                    <span>
                        <strong>{{ $totalOscs }} {{ $totalOscs === 1 ? 'organização conectada' : 'organizações conectadas' }}</strong>
                        em Camaçari
                    </span>
                </div>
            @endif
        </div>
        <div class="hero__visual">
            <img src="{{ asset('images/redcam-logo.webp') }}" alt="Logotipo Redcam: transformando intenções isoladas em ações coletivas" width="1061" height="1008" fetchpriority="high" decoding="async">
        </div>
    </section>

    <section class="quick-stats" aria-label="A rede em números">
        <article><strong>{{ $totalOscs }}</strong><span>OSCs cadastradas</span></article>
        <article><strong>{{ $demandasAtivas }}</strong><span>Demandas ativas</span></article>
        <article><strong>{{ $demandasConcluidas }}</strong><span>Demandas concluídas</span></article>
        <article>
            <strong>{{ $totalManifestacoes }}</strong>
            <span>Manifestações recebidas · {{ $manifestacoesNoMes }} neste mês</span>
        </article>
    </section>

    <section class="section" id="demandas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Ajude agora</span>
                <h2>Necessidades da nossa comunidade</h2>
                <p>Cada demanda foi publicada diretamente por uma organização da rede.</p>
            </div>
            <div class="section-counter">
                <strong>{{ $demandas->total() }}</strong>
                <span>{{ $demandas->total() === 1 ? 'demanda encontrada' : 'demandas encontradas' }}</span>
            </div>
        </div>

        @php($filtrando = $filtros['busca'] !== '' || $filtros['categoria'] || $filtros['status'] || $filtros['urgente'])

        <form class="filters" method="GET" action="{{ route('feed') }}#demandas" role="search" aria-label="Filtrar demandas">
            <label class="search-box">
                <span class="sr-only">Buscar por causa ou organização</span>
                <x-icon name="search" :size="19" />
                <input type="search" name="busca" value="{{ $filtros['busca'] }}" placeholder="Buscar por causa ou organização" maxlength="100">
            </label>
            <select name="categoria" aria-label="Filtrar por categoria" data-auto-submit>
                <option value="">Todas as categorias</option>
                @foreach (\App\Models\Demanda::CATEGORIAS as $categoria)
                    <option value="{{ $categoria }}" @selected($filtros['categoria'] === $categoria)>{{ $categoria }}</option>
                @endforeach
            </select>
            <select name="status" aria-label="Filtrar por status" data-auto-submit>
                <option value="">Todos os status</option>
                @foreach (\App\Models\Demanda::STATUS_PUBLICOS as $status)
                    <option value="{{ $status }}" @selected($filtros['status'] === $status)>{{ \App\Models\Demanda::STATUS[$status] }}</option>
                @endforeach
            </select>
            <label class="urgent-toggle">
                <input type="checkbox" name="urgente" value="1" class="sr-only" @checked($filtros['urgente']) data-auto-submit>
                <x-icon name="clock" :size="18" /> Somente urgentes
            </label>
            <button type="submit" class="button button--secondary">Filtrar</button>
        </form>

        @if ($demandas->isNotEmpty())
            <div class="demand-grid">
                @foreach ($demandas as $demanda)
                    <article class="demand-card">
                        <div class="demand-card__top">
                            <div class="org-avatar" aria-hidden="true">{{ $demanda->osc->iniciais }}</div>
                            <div class="badges">
                                @if ($demanda->urgencia)
                                    <span class="badge badge--urgent">Urgente</span>
                                @endif
                                <x-status-badge :status="$demanda->status" />
                            </div>
                        </div>
                        <div class="demand-card__body">
                            <span class="eyebrow">{{ $demanda->categoria }}</span>
                            <h3>{{ $demanda->titulo }}</h3>
                            <p class="demand-card__org">{{ $demanda->osc->nome }}</p>
                            <p class="demand-card__description">{{ $demanda->descricao }}</p>
                            <div class="demand-card__meta">
                                <span><x-icon name="box" :size="15" /> {{ $demanda->quantidade }} un.</span>
                                <span><x-icon name="location" :size="15" /> {{ $demanda->osc->endereco ?: 'Não informado' }}</span>
                            </div>
                        </div>
                        <div class="demand-card__footer">
                            <span><x-icon name="calendar" :size="15" /> {{ $demanda->prazo ? 'Até '.$demanda->prazo->format('d/m/Y') : 'Sem prazo' }}</span>
                            <a href="{{ route('demandas.show', $demanda) }}" class="button button--primary" aria-label="Quero ajudar: {{ $demanda->titulo }}">
                                Quero ajudar <x-icon name="arrow" :size="16" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{ $demandas->links('partials.paginacao') }}
        @else
            <div class="empty-state">
                <x-icon name="search" :size="36" />
                <h3>Nenhuma demanda encontrada</h3>
                @if ($filtrando)
                    <p>Tente mudar os filtros para ver outras oportunidades.</p>
                    <a href="{{ route('feed') }}#demandas" class="button button--secondary">Limpar filtros</a>
                @else
                    <p>Assim que as organizações publicarem novas necessidades, elas aparecerão aqui.</p>
                @endif
            </div>
        @endif
    </section>

    <section class="section section--tinted" id="como-funciona">
        <div class="section-heading section-heading--center">
            <div>
                <span class="eyebrow">Simples e transparente</span>
                <h2>Ajudar ficou mais fácil</h2>
                <p>Em três passos, sua vontade se transforma em impacto real.</p>
            </div>
        </div>
        <div class="steps">
            <article>
                <span class="step-number">01</span>
                <div class="step-icon"><x-icon name="search" :size="29" /></div>
                <h3>Encontre uma causa</h3>
                <p>Use os filtros para descobrir necessidades que combinam com a forma como você quer ajudar.</p>
            </article>
            <article>
                <span class="step-number">02</span>
                <div class="step-icon"><x-icon name="heart" :size="29" /></div>
                <h3>Manifeste seu interesse</h3>
                <p>Preencha um formulário rápido. Não precisa criar uma conta nem expor seus dados publicamente.</p>
            </article>
            <article>
                <span class="step-number">03</span>
                <div class="step-icon"><x-icon name="people" :size="29" /></div>
                <h3>Conecte-se com a OSC</h3>
                <p>A organização recebe seu contato e combina diretamente os próximos passos com você.</p>
            </article>
        </div>
    </section>

    <section class="section" id="sobre">
        <div class="about-section">
            <div class="about-section__copy">
                <span class="eyebrow">Uma rede feita em Camaçari</span>
                <h2>Informação centralizada. Impacto multiplicado.</h2>
                <p>A Redcam aproxima organizações sociais, apoiadores e a UOSCC em um ambiente confiável. Aqui, necessidades reais não se perdem no fluxo das redes sociais.</p>
                <ul>
                    <li><x-icon name="check" /> Demandas organizadas e atualizadas</li>
                    <li><x-icon name="check" /> Contatos protegidos pela LGPD</li>
                    <li><x-icon name="check" /> Acompanhamento do status de cada causa</li>
                </ul>
            </div>
            <div class="about-section__quote">
                <x-icon name="spark" :size="28" />
                <blockquote>“Quando a informação chega à pessoa certa, a solidariedade vira ação.”</blockquote>
                <p>Rede UOSCC · Camaçari</p>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <span class="eyebrow">Faça parte desta rede</span>
        <h2>Existe uma causa esperando por você.</h2>
        <p>Doe materiais, compartilhe conhecimento ou ofereça seu tempo. Toda contribuição começa com uma conexão.</p>
        <a href="#demandas" class="button button--light">Ver demandas abertas <x-icon name="arrow" /></a>
    </section>
</x-layouts.site>
