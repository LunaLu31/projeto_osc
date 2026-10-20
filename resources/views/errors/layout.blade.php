<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }} | Redcam</title>
    <style nonce="{{ Vite::cspNonce() }}">
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; box-sizing: border-box; background: #f5f9fd; color: #18304b; font-family: "DM Sans", system-ui, sans-serif; }
        main { max-width: 440px; padding: 40px 32px; border: 1px solid #dce8f2; border-radius: 20px; background: #fff; text-align: center; }
        span { color: #347db8; font-size: 12px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 10px 0; font-size: 26px; }
        p { color: #5e7188; line-height: 1.6; }
        a { display: inline-block; margin-top: 12px; padding: 12px 20px; border-radius: 12px; background: #347db8; color: #fff; font-weight: 700; text-decoration: none; }
    </style>
</head>

<body>
    <main>
        <span>Erro {{ $codigo }}</span>
        <h1>{{ $titulo }}</h1>
        <p>{{ $mensagem }}</p>
        <a href="{{ url('/') }}">Voltar para o início</a>
    </main>
</body>

</html>
