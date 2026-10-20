{{-- Gestão das demandas da OSC (RF03) com alteração de status (RF07). --}}
<x-layouts.osc title="Minhas demandas">
    <section class="dashboard-title">
        <div>
            <span class="eyebrow">Gestão</span>
            <h1>Minhas demandas</h1>
            <p>Publique, edite e atualize o status das necessidades da sua organização.</p>
        </div>
        <a href="{{ route('osc.demandas.create') }}" class="button button--primary"><x-icon name="plus" /> Nova demanda</a>
    </section>

    <section class="dashboard-list mt-8">
        <nav class="filtro-abas" aria-label="Filtrar por status">
            <a href="{{ route('osc.demandas.index') }}" @if (! $statusAtual) aria-current="page" @endif>
                Todas <span>{{ $totaisPorStatus->sum() }}</span>
            </a>
            @foreach (\App\Models\Demanda::STATUS as $valor => $rotulo)
                <a href="{{ route('osc.demandas.index', ['status' => $valor]) }}" @if ($statusAtual === $valor) aria-current="page" @endif>
                    {{ $rotulo }} <span>{{ $totaisPorStatus[$valor] ?? 0 }}</span>
                </a>
            @endforeach
        </nav>

        @if ($demandas->isEmpty())
            <div class="empty-state">
                <x-icon name="box" :size="36" />
                @if ($statusAtual)
                    <h3>Nenhuma demanda com este status</h3>
                    <p>Escolha outro filtro para ver as demais demandas.</p>
                @else
                    <h3>Nenhuma demanda publicada</h3>
                    <p>Publique a primeira necessidade da sua organização para que apoiadores possam ajudar.</p>
                    <a href="{{ route('osc.demandas.create') }}" class="button button--primary"><x-icon name="plus" /> Nova demanda</a>
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Demanda</th>
                            <th scope="col">Status</th>
                            <th scope="col">Prazo</th>
                            <th scope="col">Contatos</th>
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
                                    @if ($demanda->manifestacoes_count > 0)
                                        <a href="{{ route('osc.contatos.index', ['demanda' => $demanda->id_demanda]) }}" class="text-link">
                                            {{ $demanda->manifestacoes_count }} {{ $demanda->manifestacoes_count === 1 ? 'contato' : 'contatos' }}
                                        </a>
                                    @else
                                        <span class="text-muted">Nenhum ainda</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        @if ($demanda->estaPublica())
                                            <a href="{{ route('demandas.show', $demanda) }}" class="button button--secondary button--small" target="_blank" rel="noopener">Ver</a>
                                        @endif
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
</x-layouts.osc>
