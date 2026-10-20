{{-- Primeiro acesso: cria o administrador inicial (só existe enquanto não houver nenhum). --}}
<x-layouts.auth title="Configuração inicial" subtitulo="Configuração inicial">
    <span class="eyebrow">Primeiro acesso</span>
    <h1>Criar conta administrativa</h1>
    <p>Esta tela aparece somente enquanto ainda não houver um administrador cadastrado.</p>

    <form method="POST" action="{{ route('admin.configuracao') }}">
        @csrf
        <x-field name="nome" label="Nome">
            <input name="nome" value="{{ old('nome') }}" autocomplete="name" maxlength="100" required>
        </x-field>
        <x-field name="email" label="E-mail">
            <input name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="150" required>
        </x-field>
        <x-field name="password" label="Senha" hint="Mínimo de 8 caracteres, com letras e números.">
            <input name="password" type="password" autocomplete="new-password" minlength="8" required>
        </x-field>
        <x-field name="password_confirmation" label="Confirmar senha">
            <input name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
        </x-field>
        <button type="submit" class="button button--primary button--block mt-6">Criar administrador e continuar</button>
    </form>
</x-layouts.auth>
