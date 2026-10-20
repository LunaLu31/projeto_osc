{{-- Tela de sucesso após manifestar interesse (RF06). --}}
<x-layouts.site title="Interesse enviado">
    <div class="page">
        <div class="success-state" role="status">
            <div class="success-state__icon"><x-icon name="check" :size="32" /></div>
            <span class="eyebrow">Interesse enviado</span>
            <h1>Obrigado por fazer parte dessa rede!</h1>
            <p>Seus dados foram compartilhados com segurança apenas com a <strong>{{ $demanda->osc->nome }}</strong>. A organização entrará em contato com você.</p>
            <div class="privacy-note">
                <x-icon name="shield" />
                Seus dados de contato não ficam visíveis publicamente.
            </div>
            <a href="{{ route('feed') }}#demandas" class="button button--primary button--block">Voltar para as demandas</a>
        </div>
    </div>
</x-layouts.site>
