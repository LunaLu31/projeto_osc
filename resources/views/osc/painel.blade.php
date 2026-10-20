{{-- Visão geral do painel da OSC: indicadores, contatos recentes e prazos próximos. --}}
<x-layouts.osc title="Painel da OSC">
    <section class="dashboard-title">
        <div>
            <span class="eyebrow">Visão geral</span>
            <h1>Olá, {{ $osc->nome }}</h1>
            <p>Acompanhe suas demandas e conexões em um só lugar.</p>
        </div>
        <a href="{{ route('osc.demandas.create') }}" class="button button--primary"><x-icon name="plus" /> Nova demanda</a>
    </section>

    <section class="metric-grid" aria-label="Indicadores">
        <article>
            <span class="metric-icon metric-icon--blue"><x-icon name="box" /></span>
            <div>
                <strong>{{ $metricas['demandas_ativas'] }}</strong><span>Demandas ativas</span>
                <small>{{ $metricas['demandas_por_status'][\App\Models\Demanda::STATUS_PARCIAL] }} parcialmente atendidas</small>
            </div>
        </article>
        <article>
            <span class="metric-icon metric-icon--green"><x-icon name="people" /></span>
            <div>
                <strong>{{ $metricas['total_contatos'] }}</strong><span>Contatos recebidos</span>
                <small>{{ $metricas['contatos_recentes'] }} nos últimos {{ \App\Services\MetricasOsc::DIAS_RECENTES }} dias</small>
            </div>
        </article>
        <article>
            <span class="metric-icon metric-icon--orange"><x-icon name="check" /></span>
            <div><strong>{{ $metricas['demandas_concluidas'] }}</strong><span>Demandas concluídas</span></div>
        </article>
    </section>

    <div class="painel-grid">
        <section class="dashboard-list">
            <div class="section-heading--compact">
                <div>
                    <span class="eyebrow">Quem quer ajudar</span>
                    <h2>Contatos recentes</h2>
                </div>
                <a href="{{ route('osc.contatos.index') }}" class="text-link">Ver todos</a>
            </div>
            @if ($contatosRecentes->isEmpty())
                <p class="empty-state">Os contatos de quem manifestar interesse nas suas demandas aparecerão aqui.</p>
            @else
                <ul class="lista-resumo">
                    @foreach ($contatosRecentes as $contato)
                        <li>
                            <div>
                                <strong>{{ $contato->nome_apoiador }}</strong>
                                <small>{{ $contato->tipo_interesse }} · {{ $contato->demanda->titulo }}</small>
                            </div>
                            <time datetime="{{ $contato->data_manifestacao?->toIso8601String() }}">{{ $contato->data_manifestacao?->format('d/m H:i') }}</time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="dashboard-list">
            <div class="section-heading--compact">
                <div>
                    <span class="eyebrow">Atenção</span>
                    <h2>Prazos próximos</h2>
                </div>
                <span>próximos {{ $diasPrazoProximo }} dias</span>
            </div>
            @if ($prazosProximos->isEmpty())
                <p class="empty-state">Nenhuma demanda ativa vence nos próximos {{ $diasPrazoProximo }} dias.</p>
            @else
                <ul class="lista-resumo">
                    @foreach ($prazosProximos as $demanda)
                        <li>
                            <div>
                                <strong>{{ $demanda->titulo }}</strong>
                                <small><x-status-badge :status="$demanda->status" /> @if ($demanda->urgencia) <span class="badge badge--urgent">Urgente</span> @endif</small>
                            </div>
                            <a href="{{ route('osc.demandas.edit', $demanda) }}" class="text-link">{{ $demanda->prazo->format('d/m/Y') }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.osc>
