{{-- Layout da área pública (apoiadores). --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('partials.head', ['title' => $title])
</head>

<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>

    <header class="site-header">
        <div class="navigation-menu">
            <button type="button" class="menu-button" data-menu-toggle aria-controls="menu-principal" aria-expanded="false" aria-label="Abrir menu de navegação">
                <x-icon name="menu" class="menu-button__open" />
                <x-icon name="close" class="menu-button__close" />
            </button>
            <nav id="menu-principal" class="nav" aria-label="Navegação principal">
                <a href="{{ route('feed') }}#demandas"><x-icon name="home" /> Demandas</a>
                <a href="{{ route('feed') }}#como-funciona"><x-icon name="spark" /> Como funciona</a>
                <a href="{{ route('feed') }}#sobre"><x-icon name="people" /> Sobre a rede</a>
                <a href="{{ route('osc.login') }}"><x-icon name="user" /> Área da OSC</a>
            </nav>
        </div>
        <x-logo />
    </header>

    <main id="conteudo">
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <div>
            <x-logo />
            <p>Conectando quem quer ajudar com quem transforma Camaçari todos os dias.</p>
        </div>
        <div>
            <strong>Navegue</strong>
            <a href="{{ route('feed') }}#demandas">Demandas</a>
            <a href="{{ route('feed') }}#como-funciona">Como funciona</a>
            <a href="{{ route('osc.login') }}">Área da OSC</a>
        </div>
        <div class="footer-signature">
            <strong>UOSCC / Redcam</strong>
            <span>Um projeto pela comunidade de Camaçari.</span>
        </div>
    </footer>
    <div class="footer-bottom">
        <span>© {{ now()->year }} Redcam. Todos os direitos reservados.</span>
        <span>MVP · Versão A</span>
    </div>
</body>

</html>
