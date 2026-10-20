{{-- Login da OSC (RF01). --}}
<x-layouts.auth title="Área da OSC" subtitulo="Área da OSC">
    <span class="eyebrow">Área restrita</span>
    <h1>Acesse sua organização</h1>
    <p>Gerencie suas demandas e acompanhe quem quer ajudar.</p>

    <form method="POST" action="{{ route('osc.login') }}">
        @csrf
        <x-field name="email" label="E-mail">
            <input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        </x-field>
        <x-field name="password" label="Senha">
            <input name="password" type="password" autocomplete="current-password" required>
        </x-field>
        <button type="submit" class="button button--primary button--block mt-6">Entrar no painel <x-icon name="arrow" /></button>
    </form>

    <p class="auth-card__footer field__hint">Esqueceu a senha? Fale com a equipe UOSCC para gerar uma nova.</p>
    <div class="auth-card__footer">
        <a href="{{ route('feed') }}" class="back-link mb-0"><x-icon name="back" :size="16" /> Voltar para a página pública</a>
    </div>
</x-layouts.auth>
