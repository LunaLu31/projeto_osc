{{-- Detalhe público da demanda + formulário "Quero ajudar" (RF05). --}}
<x-layouts.site :title="$demanda->titulo">
    <div class="page">
        <a href="{{ route('feed') }}#demandas" class="back-link"><x-icon name="back" :size="16" /> Voltar para as demandas</a>

        <article class="detail-card">
            <div class="detail-layout">
                <aside class="detail-aside">
                    <span class="eyebrow">Você está ajudando</span>
                    <div class="org-avatar org-avatar--large" aria-hidden="true">{{ $demanda->osc->iniciais }}</div>
                    <h2>{{ $demanda->osc->nome }}</h2>
                    <span class="detail-aside__area">{{ $demanda->osc->area_atuacao }}</span>
                    @if ($demanda->osc->descricao)
                        <p>{{ $demanda->osc->descricao }}</p>
                    @endif
                    <div class="verified">
                        <x-icon name="shield" :size="18" />
                        Organização cadastrada pela UOSCC
                    </div>
                </aside>

                <section class="detail-content">
                    <div class="badges">
                        @if ($demanda->urgencia)
                            <span class="badge badge--urgent">Urgente</span>
                        @endif
                        <x-status-badge :status="$demanda->status" />
                    </div>
                    <span class="eyebrow">{{ $demanda->categoria }}</span>
                    <h1>{{ $demanda->titulo }}</h1>
                    <p class="detail-description">{{ $demanda->descricao }}</p>
                    <div class="detail-grid">
                        <div>
                            <x-icon name="box" :size="18" />
                            <span>Quantidade</span>
                            <strong>{{ $demanda->quantidade }} un.</strong>
                        </div>
                        <div>
                            <x-icon name="calendar" :size="18" />
                            <span>Prazo</span>
                            <strong>{{ $demanda->prazo?->format('d/m/Y') ?? 'Sem prazo' }}</strong>
                        </div>
                        <div>
                            <x-icon name="location" :size="18" />
                            <span>Localização</span>
                            <strong>{{ $demanda->osc->endereco ?: 'Não informado' }}</strong>
                        </div>
                    </div>
                </section>
            </div>

            <form id="quero-ajudar" class="interest-form" method="POST" action="{{ route('demandas.interesse', $demanda) }}">
                @csrf
                <span class="eyebrow">Falta pouco</span>
                <h2>Como você quer ajudar?</h2>
                <p>Não é necessário criar uma conta. A <strong>{{ $demanda->osc->nome }}</strong> receberá seu contato.</p>

                @if ($errors->any())
                    <div class="alert alert--error mt-5" role="alert">
                        <x-icon name="warning" />
                        <span>Revise os campos destacados para enviar seu interesse.</span>
                    </div>
                @endif

                <div class="form-grid">
                    <x-field name="nome_apoiador" label="Seu nome">
                        <input name="nome_apoiador" type="text" value="{{ old('nome_apoiador') }}" placeholder="Nome completo" maxlength="100" autocomplete="name" required>
                    </x-field>
                    <x-field name="tipo_interesse" label="Tipo de contribuição">
                        <select name="tipo_interesse" required>
                            <option value="" disabled @selected(! old('tipo_interesse'))>Selecione uma opção</option>
                            @foreach ($tiposInteresse as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tipo_interesse') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field name="telefone" label="Telefone" hint="Informe telefone, e-mail ou ambos.">
                        <input name="telefone" type="tel" value="{{ old('telefone') }}" placeholder="(71) 99999-9999" maxlength="20" autocomplete="tel">
                    </x-field>
                    <x-field name="email" label="E-mail">
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="voce@email.com" maxlength="150" autocomplete="email">
                    </x-field>
                </div>
                <x-field name="mensagem" label="Mensagem" :optional="true">
                    <textarea name="mensagem" rows="4" maxlength="2000" placeholder="Conte brevemente como você pode contribuir...">{{ old('mensagem') }}</textarea>
                </x-field>

                <div class="privacy-note">
                    <x-icon name="shield" />
                    Seus dados serão enviados somente para esta organização e não ficam visíveis publicamente.
                </div>

                <label class="checkbox-field">
                    <input type="checkbox" name="consentimento" value="1" @checked(old('consentimento')) required>
                    <span>Concordo em compartilhar meus dados com esta OSC para retorno sobre esta demanda.</span>
                </label>
                @error('consentimento')
                    <span class="field__error">{{ $message }}</span>
                @enderror

                <x-anti-spam />

                <div class="form-actions">
                    <button type="submit" class="button button--primary">Enviar interesse <x-icon name="arrow" /></button>
                </div>
            </form>
        </article>
    </div>
</x-layouts.site>
