{{-- Criação e edição de demanda pela OSC (RF03). --}}
@php($editando = $demanda->exists)
@php($osc = auth('osc')->user())
<x-layouts.painel :title="$editando ? 'Editar demanda' : 'Nova demanda'" :nome="$osc->nome" perfil="Perfil da OSC" :iniciais="$osc->iniciais" :inicio="route('osc.painel')" :logout="route('osc.logout')">
    <div class="page--narrow">
        <a href="{{ route('osc.painel') }}" class="back-link"><x-icon name="back" :size="16" /> Voltar para o painel</a>

        <form method="POST" action="{{ $editando ? route('osc.demandas.update', $demanda) : route('osc.demandas.store') }}" class="form-card">
            @csrf
            @if ($editando)
                @method('PUT')
            @endif

            <div class="form-card__header">
                <span class="eyebrow">{{ $editando ? 'Edição' : 'Nova publicação' }}</span>
                <h1>{{ $editando ? 'Editar demanda' : 'Criar demanda' }}</h1>
                <p>Descreva de forma clara o que sua organização precisa.</p>
            </div>

            <x-field name="titulo" label="Título">
                <input name="titulo" type="text" value="{{ old('titulo', $demanda->titulo) }}" placeholder="Ex.: 30 kits de material escolar" maxlength="150" required>
            </x-field>
            <div class="form-grid">
                <x-field name="categoria" label="Categoria">
                    <select name="categoria" required>
                        <option value="">Selecione</option>
                        @foreach (\App\Models\Demanda::CATEGORIAS as $categoria)
                            <option value="{{ $categoria }}" @selected(old('categoria', $demanda->categoria) === $categoria)>{{ $categoria }}</option>
                        @endforeach
                    </select>
                </x-field>
                <x-field name="quantidade" label="Quantidade">
                    <input name="quantidade" type="number" min="1" max="1000000" value="{{ old('quantidade', $demanda->quantidade) }}" placeholder="30" required>
                </x-field>
            </div>
            <x-field name="descricao" label="Descrição">
                <textarea name="descricao" rows="5" maxlength="5000" placeholder="Explique a necessidade, para quem é e como entregar." required>{{ old('descricao', $demanda->descricao) }}</textarea>
            </x-field>
            <x-field name="prazo" label="Prazo">
                <input name="prazo" type="date" value="{{ old('prazo', $demanda->prazo?->format('Y-m-d')) }}" @unless ($editando) min="{{ today()->format('Y-m-d') }}" @endunless required>
            </x-field>
            <label class="checkbox-field">
                <input name="urgencia" type="checkbox" value="1" @checked(old('urgencia', $demanda->urgencia))>
                <span><strong>Demanda urgente</strong><br>Aparece em destaque no topo do feed.</span>
            </label>

            <div class="form-actions">
                <a href="{{ route('osc.painel') }}" class="button button--secondary">Cancelar</a>
                <button type="submit" class="button button--primary">{{ $editando ? 'Salvar alterações' : 'Publicar demanda' }}</button>
            </div>
        </form>
    </div>
</x-layouts.painel>
