{{-- Layout dos painéis privados (OSC e administrador). --}}
@props(['title', 'nome', 'perfil', 'iniciais', 'inicio', 'logout'])
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('partials.head', ['title' => $title])
    <meta name="robots" content="noindex">
</head>

<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>

    <header class="dashboard-header">
        <x-logo :href="$inicio" :subtitulo="$perfil" />
        <div class="dashboard-user">
            {{ $menu ?? '' }}
            <div class="org-avatar" aria-hidden="true">{{ $iniciais }}</div>
            <span class="dashboard-user__info">
                <strong>{{ $nome }}</strong>
                <small>{{ $perfil }}</small>
            </span>
            <form method="POST" action="{{ $logout }}">
                @csrf
                <button type="submit" class="button button--ghost button--small">Sair</button>
            </form>
        </div>
    </header>

    <main id="conteudo" class="dashboard-main">
        {{ $slot }}
    </main>
</body>

</html>
