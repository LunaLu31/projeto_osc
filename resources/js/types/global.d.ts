import type { Config } from 'ziggy-js';
import type { PropsCompartilhadas } from './index';

declare module '@inertiajs/core' {
    // Tipagem das props compartilhadas em usePage().props.
    // eslint-disable-next-line @typescript-eslint/no-empty-object-type
    interface PageProps extends PropsCompartilhadas {}
}

interface TurnstileApi {
    render(
        elemento: HTMLElement,
        opcoes: {
            sitekey: string;
            language?: string;
            theme?: 'light' | 'dark' | 'auto';
            callback?: (token: string) => void;
            'expired-callback'?: () => void;
            'error-callback'?: () => void;
        },
    ): string;
    remove(widgetId: string): void;
}

declare global {
    // Configuração de rotas do Ziggy (definida em app.tsx a partir das props).
    var Ziggy: Config;

    interface Window {
        turnstile?: TurnstileApi;
    }
}
