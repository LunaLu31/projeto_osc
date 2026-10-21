/*
 * Renderização no servidor (SSR): o Node gera o HTML de cada página para que
 * buscadores e prévias de links (WhatsApp, redes sociais) recebam o conteúdo pronto.
 * Build: `npm run build:ssr` · Servidor: `php artisan inertia:start-ssr`.
 */
import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import { renderToString } from 'react-dom/server';
import AvisoConexao from '@/components/AvisoConexao';
import { resolverPagina, tituloDaPagina } from '@/lib/paginas';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: tituloDaPagina,
        resolve: resolverPagina,
        setup: ({ App, props }) => {
            // O render é síncrono por requisição; a configuração de rotas vale para esta página.
            globalThis.Ziggy = props.initialPage.props.ziggy;

            return (
                <>
                    <App {...props} />
                    <AvisoConexao />
                </>
            );
        },
    }),
);
