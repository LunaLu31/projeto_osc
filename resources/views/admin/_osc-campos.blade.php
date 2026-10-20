{{-- Campos da OSC compartilhados pelo cadastro e pela edição (RF02). --}}
@php($osc ??= null)
<x-field name="nome" label="Nome da organização">
    <input name="nome" value="{{ old('nome', $osc?->nome) }}" maxlength="150" required>
</x-field>
<div class="form-grid">
    <x-field name="area_atuacao" label="Área de atuação">
        <input name="area_atuacao" value="{{ old('area_atuacao', $osc?->area_atuacao) }}" maxlength="100" placeholder="Ex.: Educação" required>
    </x-field>
    <x-field name="telefone" label="Telefone">
        <input name="telefone" type="tel" value="{{ old('telefone', $osc?->telefone) }}" maxlength="20" placeholder="(71) 99999-9999" required>
    </x-field>
</div>
<x-field name="endereco" label="Endereço">
    <input name="endereco" value="{{ old('endereco', $osc?->endereco) }}" maxlength="255" placeholder="Bairro, Camaçari - BA" required>
</x-field>
<x-field name="descricao" label="Descrição">
    <textarea name="descricao" rows="3" maxlength="5000" required>{{ old('descricao', $osc?->descricao) }}</textarea>
</x-field>
<x-field name="email" label="E-mail de acesso da OSC">
    <input name="email" type="email" value="{{ old('email', $osc?->email) }}" maxlength="150" autocomplete="off" required>
</x-field>
<div class="form-grid">
    <x-field name="password" :label="$osc ? 'Nova senha' : 'Senha inicial'" :optional="true"
        :hint="$osc ? 'Deixe em branco para manter a atual.' : 'Deixe em branco para gerar automaticamente.'">
        <input name="password" type="password" minlength="8" autocomplete="new-password">
    </x-field>
    <x-field name="password_confirmation" label="Confirmar senha">
        <input name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
    </x-field>
</div>
