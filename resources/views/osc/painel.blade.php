{{-- Painel da OSC: demandas próprias, status e contatos dos interessados (RF03, RF07, RF08). --}}
<x-layouts.painel title="Painel da OSC" :nome="$osc->nome" perfil="Perfil da OSC" :iniciais="$osc->iniciais" :inicio="route('osc.painel')" :logout="route('osc.logout')">
    <x-slot:menu>
        <a href="{{ route('osc.senha.edit') }}" class="button button--ghost button--small"><x-icon name="key" :size="16" /> Alterar senha</a>
    </x-slot:menu>

    <x-alerts />

    <section class="dashboard-title">
        <div>
            <span class="eyebrow">Painel da organização</span>
            <h1>Olá, {{ $osc->nome }}</h1>
            <p>Acompanhe suas demandas e conexões em um só lugar.</p>
        </div>
        <a href="{{ route('osc.demandas.create') }}" class="button button--primary"><x-icon name="plus" /> Nova demanda</a>
    </section>

    <section class="metric-grid" aria-label="Resumo">
        <article>
            <span class="metric-icon metric-icon--blue"><x-icon name="box" /></span>
            <div><strong>{{ $totalAtivas }}</strong><span>Demandas ativas</span></div>
        </article>
        <article>
            <span class="metric-icon metric-icon--green"><x-icon name="people" /></span>
            <div><strong>{{ $totalInteressados }}</strong><span>Interessados</span></div>
        </article>
        <article>
            <span class="metric-icon metric-icon--orange"><x-icon name="check" /></span>
            <div><strong>{{ $totalConcluidas }}</strong><span>Demandas concluídas</span></div>
        </article>
    </section>

    <section class="dashboard-list">
        <div class="section-heading--compact">
            <div>
                <span class="eyebrow">Gestão</span>
                <h2>Minhas demandas</h2>
            </div>
            <span>{{ $demandas->count() }} no total</span>
        </div>

        @if ($demandas->isEmpty())
            <div class="empty-state">
                <x-icon name="box" :size="36" />
                <h3>Nenhuma demanda publicada</h3>
                <p>Publique a primeira necessidade da sua organização para que apoiadores possam ajudar.</p>
                <a href="{{ route('osc.demandas.create') }}" class="button button--primary"><x-icon name="plus" /> Nova demanda</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Demanda</th>
                            <th scope="col">Status</th>
                            <th scope="col">Prazo</th>
                            <th scope="col">Interessados</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demandas as $demanda)
                            <tr>
                                <td>
                                    <div class="data-table__title">
                                        <strong>{{ $demanda->titulo }}</strong>
                                        <small>
                                            {{ $demanda->categoria }} · {{ $demanda->quantidade }} un.
                                            @if ($demanda->urgencia)
                                                · <span class="font-bold text-coral">Urgente</span>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('osc.demandas.status', $demanda) }}" class="status-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" aria-label="Status de {{ $demanda->titulo }}" data-auto-submit>
                                            @foreach (\App\Models\Demanda::STATUS as $valor => $rotulo)
                                                <option value="{{ $valor }}" @selected($demanda->status === $valor)>{{ $rotulo }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="button button--secondary button--small">Salvar</button>
                                    </form>
                                </td>
                                <td>{{ $demanda->prazo?->format('d/m/Y') ?? 'Sem prazo' }}</td>
                                <td>
                                    @if ($demanda->manifestacoes->isEmpty())
                                        <span class="text-muted">Nenhum ainda</span>
                                    @else
                                        <details class="contacts">
                                            <summary>{{ $demanda->manifestacoes->count() }} {{ $demanda->manifestacoes->count() === 1 ? 'contato' : 'contatos' }}</summary>
                                            <ul>
                                                @foreach ($demanda->manifestacoes as $interesse)
                                                    <li>
                                                        <strong>{{ $interesse->nome_apoiador }}</strong>
                                                        <span class="contacts__meta">· {{ $interesse->tipo_interesse }} · {{ $interesse->data_manifestacao?->format('d/m/Y H:i') }}</span>
                                                        @if ($interesse->telefone)
                                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $interesse->telefone) }}">{{ $interesse->telefone }}</a>
                                                        @endif
                                                        @if ($interesse->email)
                                                            <a href="mailto:{{ $interesse->email }}">{{ $interesse->email }}</a>
                                                        @endif
                                                        @if ($interesse->mensagem)
                                                            <p>{{ $interesse->mensagem }}</p>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('osc.demandas.edit', $demanda) }}" class="button button--secondary button--small"><x-icon name="edit" :size="15" /> Editar</a>
                                        <form method="POST" action="{{ route('osc.demandas.destroy', $demanda) }}" data-confirm="Excluir a demanda “{{ $demanda->titulo }}” e as manifestações recebidas?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button--danger button--small"><x-icon name="trash" :size="15" /> Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.painel>
