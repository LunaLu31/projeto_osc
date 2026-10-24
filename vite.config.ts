import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx', 'resources/js/swagger.ts'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            // React Compiler: memoriza componentes e valores automaticamente, evitando
            // renderizações desnecessárias sem useMemo/useCallback escritos à mão.
            babel: { plugins: ['babel-plugin-react-compiler'] },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    build: {
        // O Swagger UI (~1,5 MB) só é carregado na página /docs/api.
        chunkSizeWarningLimit: 2000,
    },
    server: {
        // IPv4 explícito: a Content Security Policy não aceita IPv6 literal ([::1]) como origem.
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
