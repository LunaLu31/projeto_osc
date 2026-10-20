<?php

return [

    /*
    | Content Security Policy (CSP)
    | Restringe de onde a página pode carregar scripts, estilos, fontes e frames.
    | Use CSP_REPORT_ONLY=true para observar violações no console sem bloquear.
    */
    'csp' => [
        'ativa' => (bool) env('CSP_ENABLED', true),
        'somente_relatorio' => (bool) env('CSP_REPORT_ONLY', false),
    ],

    /*
    | HTTP Strict Transport Security: obriga o navegador a usar HTTPS.
    | Só é enviado em produção e em requisições HTTPS.
    */
    'hsts_max_age' => (int) env('HSTS_MAX_AGE', 31536000),

];
