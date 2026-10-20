{{-- Login do administrador (RF01). --}}
<x-layouts.auth title="Portal administrativo" subtitulo="Portal administrativo">
    <span class="eyebrow">Acesso restrito</span>
    <h1>Entrar na gestão</h1>
    <p>Gerencie as OSCs da rede e acompanhe as demandas publicadas.</p>

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <x-field name="email" label="E-mail">
            <input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        </x-field>
        <x-field name="password" label="Senha">
            <input name="password" type="password" autocomplete="current-password" required>
        </x-field>
        <button type="submit" class="button button--primary button--block mt-6">Entrar no portal <x-icon name="arrow" /></button>
    </form>
</x-layouts.auth>
