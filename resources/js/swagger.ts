/*
 * Swagger UI da API CONECTA_OSC, empacotado pelo Vite (sem CDN, compatível com a CSP).
 * A especificação OpenAPI é gerada pelo Scramble a partir do código.
 */
// Bundle de navegador (o index do pacote importa o módulo `path` do Node).
import SwaggerUIBundle from 'swagger-ui-dist/swagger-ui-bundle.js';
import 'swagger-ui-dist/swagger-ui.css';
import '../css/swagger.css';

const alvo = document.getElementById('swagger-ui');

if (alvo) {
    SwaggerUIBundle({
        url: alvo.dataset.especificacao,
        domNode: alvo,
        deepLinking: true,
        docExpansion: 'list',
        filter: true,
        displayRequestDuration: true,
        persistAuthorization: true,
        tryItOutEnabled: true,
        defaultModelsExpandDepth: 0,
        requestInterceptor: (requisicao: { headers: Record<string, string> }) => {
            requisicao.headers.Accept = 'application/json';
            return requisicao;
        },
    });
}
