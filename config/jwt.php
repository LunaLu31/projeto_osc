<?php

/*
| Login por JWT (HS256) de OSCs e administradores: no site, em cookie HttpOnly; na API, no
| cabeçalho Authorization: Bearer. Implementação em app/Auth (TokensJwt e JwtGuard).
*/
return [
    /*
    | Chave da assinatura, com pelo menos 32 caracteres. Sem JWT_SECRET, é derivada da APP_KEY.
    | Trocar a chave encerra todos os logins. Gere uma com: php -r "echo base64_encode(random_bytes(32));"
    */
    'segredo' => env('JWT_SECRET'),

    /*
    | Validade em minutos. Com "Manter conectado" (e nos tokens da API), 7 dias; sem ele, 2 horas e
    | o cookie some ao fechar o navegador. No site, o token é renovado enquanto a pessoa usa o
    | sistema, então só expira depois desse tempo sem acesso.
    */
    'validade_minutos' => (int) env('JWT_TTL', 10080),
    'validade_sem_lembrar_minutos' => (int) env('JWT_TTL_SEM_LEMBRAR', 120),
];
