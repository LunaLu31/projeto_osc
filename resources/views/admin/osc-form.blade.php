{{-- Edição de uma OSC pelo administrador (RF09). --}}
@php($admin = auth('admin')->user())
<x-layouts.painel :title="'Editar '.$osc->nome" :nome="$admin->nome" perfil="Administrador UOSCC" :iniciais="$admin->iniciais" :inicio="route('admin.painel')" :logout="route('admin.logout')">
    <div class="page--narrow">
        <a href="{{ route('admin.painel') }}" class="back-link"><x-icon name="back" :size="16" /> Voltar para o painel</a>

        <form method="POST" action="{{ route('admin.oscs.update', $osc) }}" class="form-card">
            @csrf
            @method('PUT')
            <div class="form-card__header">
                <span class="eyebrow">Gestão da rede</span>
                <h1>Editar organização</h1>
                <p>Atualize os dados públicos e o acesso de <strong>{{ $osc->nome }}</strong>.</p>
            </div>

            <x-alerts />
            @include('admin._osc-campos', ['osc' => $osc])

            <div class="form-actions">
                <a href="{{ route('admin.painel') }}" class="button button--secondary">Cancelar</a>
                <button type="submit" class="button button--primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</x-layouts.painel>
