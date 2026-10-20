import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/swagger.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // O Swagger UI (~1,8 MB) só é carregado na página /docs/api.
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
