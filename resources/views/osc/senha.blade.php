{{-- Troca da senha inicial da OSC. --}}
<x-layouts.osc title="Alterar senha">
    <div class="page--narrow">
        <form method="POST" action="{{ route('osc.senha.update') }}" class="form-card">
            @csrf
            @method('PUT')

            <div class="form-card__header">
                <span class="eyebrow">Segurança</span>
                <h1>Alterar senha</h1>
                <p>Troque a senha inicial recebida da equipe UOSCC por uma que só a sua organização conheça.</p>
            </div>

            <x-field name="senha_atual" label="Senha atual">
                <input name="senha_atual" type="password" autocomplete="current-password" required>
            </x-field>
            <div class="form-grid">
                <x-field name="password" label="Nova senha" hint="Mínimo de 8 caracteres, com letras e números.">
                    <input name="password" type="password" autocomplete="new-password" minlength="8" required>
                </x-field>
                <x-field name="password_confirmation" label="Confirmar nova senha">
                    <input name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                </x-field>
            </div>

            <div class="form-actions">
                <a href="{{ route('osc.painel') }}" class="button button--secondary">Cancelar</a>
                <button type="submit" class="button button--primary">Salvar nova senha</button>
            </div>
        </form>
    </div>
</x-layouts.osc>
