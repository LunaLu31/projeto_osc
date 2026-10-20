{{-- Layout das telas de acesso (login da OSC/admin e configuração inicial). --}}
@props(['title', 'subtitulo' => 'Rede que transforma'])
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('partials.head', ['title' => $title])
</head>

<body>
    <main id="conteudo" class="auth-shell">
        <div class="auth-card">
            <x-logo :subtitulo="$subtitulo" />
            {{ $slot }}
        </div>
    </main>
</body>

</html>
