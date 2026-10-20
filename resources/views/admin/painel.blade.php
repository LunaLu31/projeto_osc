{{-- Painel do administrador: métricas globais e gestão da rede (RF02, RF07, RF09). --}}
<x-layouts.painel title="Painel administrativo" :nome="$admin->nome" perfil="Administrador UOSCC" :iniciais="$admin->iniciais" :inicio="route('admin.painel')" :logout="route('admin.logout')">
    <x-alerts />
    @if (session('senha_inicial'))
        <div class="alert alert--warning" role="status">
            <x-icon name="key" />
            <span>Senha inicial da OSC (exibida uma única vez): <strong class="select-all">{{ session('senha_inicial') }}</strong>. Repasse por um canal seguro e oriente a troca no primeiro acesso.</span>
        </div>
    @endif

    <section class="dashboard-title">
        <div>
            <span class="eyebrow">Painel administrativo</span>
            <h1>Olá, {{ $admin->nome }}</h1>
            <p>Acompanhe todas as OSCs e demandas da rede.</p>
        </div>
    </section>

    <section class="metric-grid" aria-label="Métricas da rede">
        <article>
            <span class="metric-icon metric-icon--blue"><x-icon name="people" /></span>
            <div><strong>{{ $oscs->count() }}</strong><span>OSCs cadastradas</span></div>
        </article>
        <article>
            <span class="metric-icon metric-icon--green"><x-icon name="box" /></span>
            <div>
                <strong>{{ $demandas->count() }}</strong><span>Demandas na rede</span>
                <small>
                    @foreach (\App\Models\Demanda::STATUS as $valor => $rotulo)
                        {{ $rotulo }}: {{ $demandasPorStatus[$valor] ?? 0 }}@if (! $loop->last) · @endif
                    @endforeach
                </small>
            </div>
        </article>
        <article>
            <span class="metric-icon metric-icon--orange"><x-icon name="heart" /></span>
            <div><strong>{{ $totalManifestacoes }}</strong><span>Manifestações de interesse</span></div>
        </article>
    </section>

    <div class="admin-grid">
        <section class="dashboard-list">
            <div class="section-heading--compact">
                <div>
                    <span class="eyebrow">Cadastro</span>
                    <h2>Cadastrar OSC</h2>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.oscs.store') }}">
                @csrf
                @include('admin._osc-campos')
                <div class="form-actions">
                    <button type="submit" class="button button--primary button--block"><x-icon name="plus" /> Cadastrar organização</button>
                </div>
            </form>
        </section>

        <div>
            <section class="dashboard-list">
                <div class="section-heading--compact">
                    <div>
                        <span class="eyebrow">Rede</span>
                        <h2>OSCs cadastradas</h2>
                    </div>
                    <span>{{ $oscs->count() }} no total</span>
                </div>
                @if ($oscs->isEmpty())
                    <p class="empty-state">Ainda não há OSCs cadastradas.</p>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Organização</th>
                                    <th scope="col">Área</th>
                                    <th scope="col">Demandas</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($oscs as $osc)
                                    <tr>
                                        <td>
                                            <div class="data-table__title"><strong>{{ $osc->nome }}</strong><small>{{ $osc->email }}</small></div>
                                        </td>
                                        <td>{{ $osc->area_atuacao }}</td>
                                        <td>{{ $osc->demandas_count }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('admin.oscs.edit', $osc) }}" class="button button--secondary button--small"><x-icon name="edit" :size="15" /> Editar</a>
                                                <form method="POST" action="{{ route('admin.oscs.destroy', $osc) }}" data-confirm="Excluir “{{ $osc->nome }}”, todas as suas demandas e manifestações recebidas?">
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
        </div>
    </div>

    <section class="dashboard-list mt-5">
        <div class="section-heading--compact">
            <div>
                <span class="eyebrow">Moderação</span>
                <h2>Demandas da rede</h2>
            </div>
            <span>{{ $demandas->count() }} no total</span>
        </div>
        @if ($demandas->isEmpty())
            <p class="empty-state">As demandas publicadas pelas OSCs aparecerão aqui.</p>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Demanda</th>
                            <th scope="col">OSC</th>
                            <th scope="col">Status</th>
                            <th scope="col">Interesses</th>
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
                                            {{ $demanda->categoria }} · criada em {{ $demanda->data_criacao?->format('d/m/Y') ?? '—' }}
                                            @if ($demanda->urgencia)
                                                · <span class="font-bold text-coral">Urgente</span>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>{{ $demanda->osc->nome }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.demandas.status', $demanda) }}" class="status-form">
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
                                <td>{{ $demanda->manifestacoes_count }}</td>
                                <td>
                                    <div class="table-actions">
                                        @if ($demanda->estaPublica())
                                            <a href="{{ route('demandas.show', $demanda) }}" class="button button--secondary button--small" target="_blank" rel="noopener">Ver</a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.demandas.destroy', $demanda) }}" data-confirm="Excluir a demanda “{{ $demanda->titulo }}” e as manifestações recebidas?">
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
