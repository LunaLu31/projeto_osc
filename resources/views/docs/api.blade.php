<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>API REST | Redcam</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/js/swagger.js')
</head>

<body>
    <header class="docs-cabecalho">
        <a href="{{ route('feed') }}">REDCAM</a>
        <span>API REST v{{ config('scramble.info.version') }} · OpenAPI 3.1 · <a href="{{ route('docs.api.json') }}">especificação JSON</a></span>
    </header>
    <main id="swagger-ui" data-especificacao="{{ route('docs.api.json') }}"></main>
</body>

</html>
