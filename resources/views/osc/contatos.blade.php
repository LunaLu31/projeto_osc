{{-- Contatos recebidos pela OSC (RF08). Visíveis só para a OSC responsável (RNF06). --}}
<x-layouts.osc title="Contatos recebidos">
    <section class="dashboard-title">
        <div>
            <span class="eyebrow">Quem quer ajudar</span>
            <h1>Contatos recebidos</h1>
            <p>Pessoas que manifestaram interesse nas demandas da sua organização.</p>
        </div>
    </section>

    <div class="privacy-note">
        <x-icon name="shield" />
        Use estes dados apenas para responder sobre a demanda indicada. Eles foram compartilhados com o consentimento do apoiador (LGPD).
    </div>

    <section class="dashboard-list">
        <form class="filtros-painel" method="GET" action="{{ route('osc.contatos.index') }}" aria-label="Filtrar contatos">
            <label class="field">
                <span class="field__label">Demanda</span>
                <select name="demanda" data-auto-submit>
                    <option value="">Todas as demandas</option>
                    @foreach ($demandas as $demanda)
                        <option value="{{ $demanda->id_demanda }}" @selected($filtros['demanda'] === $demanda->id_demanda)>{{ $demanda->titulo }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field">
                <span class="field__label">Tipo de contribuição</span>
                <select name="tipo" data-auto-submit>
                    <option value="">Todos os tipos</option>
                    @foreach ($tiposInteresse as $tipo)
                        <option value="{{ $tipo }}" @selected($filtros['tipo'] === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="button button--secondary">Filtrar</button>
        </form>

        @if ($contatos->isEmpty())
            <div class="empty-state">
                <x-icon name="people" :size="36" />
                <h3>Nenhum contato encontrado</h3>
                <p>{{ $filtros['demanda'] || $filtros['tipo'] ? 'Tente outros filtros.' : 'Quando alguém manifestar interesse nas suas demandas, o contato aparecerá aqui.' }}</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Apoiador</th>
                            <th scope="col">Contato</th>
                            <th scope="col">Demanda</th>
                            <th scope="col">Mensagem</th>
                            <th scope="col">Recebido em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contatos as $contato)
                            <tr>
                                <td>
                                    <div class="data-table__title">
                                        <strong>{{ $contato->nome_apoiador }}</strong>
                                        <small>{{ $contato->tipo_interesse }}</small>
                                    </div>
                                </td>
                                <td class="contato-meios">
                                    @if ($contato->telefone)
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contato->telefone) }}"><x-icon name="phone" :size="15" /> {{ $contato->telefone }}</a>
                                    @endif
                                    @if ($contato->email)
                                        <a href="mailto:{{ $contato->email }}"><x-icon name="mail" :size="15" /> {{ $contato->email }}</a>
                                    @endif
                                </td>
                                <td>{{ $contato->demanda->titulo }}</td>
                                <td class="contato-mensagem">{{ $contato->mensagem ?: '—' }}</td>
                                <td>{{ $contato->data_manifestacao?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $contatos->links('partials.paginacao') }}
        @endif
    </section>
</x-layouts.osc>
