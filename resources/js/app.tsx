import { createInertiaApp } from '@inertiajs/react';
import { StrictMode } from 'react';
import { createRoot, hydrateRoot } from 'react-dom/client';
import AvisoConexao from '@/components/AvisoConexao';
import { resolverPagina, tituloDaPagina } from '@/lib/paginas';

createInertiaApp({
    title: tituloDaPagina,
    resolve: resolverPagina,
    setup({ el, App, props }) {
        // Rotas nomeadas do Laravel para a função route() do ziggy-js.
        globalThis.Ziggy = props.initialPage.props.ziggy;

        const app = (
            <StrictMode>
                <App {...props} />
                <AvisoConexao />
            </StrictMode>
        );

        // Com SSR o HTML já vem pronto do servidor: o React apenas o "hidrata".
        if (el.hasChildNodes()) {
            hydrateRoot(el, app);
        } else {
            createRoot(el).render(app);
        }
    },
    // O CSS da barra de progresso está em resources/css (sem <style> inline, compatível com a CSP).
    progress: { color: '#347db8', includeCSS: false, showSpinner: false },
});
