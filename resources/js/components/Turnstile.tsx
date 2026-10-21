import { useEffect, useRef } from 'react';

const SCRIPT = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';

let carregamento: Promise<void> | null = null;

/** Carrega o script do Turnstile uma única vez (domínio liberado na CSP quando ativo). */
function carregarScript(): Promise<void> {
    carregamento ??= new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = SCRIPT;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Falha ao carregar o Turnstile'));
        document.head.appendChild(script);
    });

    return carregamento;
}

interface TurnstileProps {
    siteKey: string;
    onToken: (token: string) => void;
}

/** Widget anti-robô do Cloudflare Turnstile, renderizado de forma explícita (compatível com navegação SPA). */
export default function Turnstile({ siteKey, onToken }: TurnstileProps) {
    const alvo = useRef<HTMLDivElement>(null);
    const callback = useRef(onToken);

    useEffect(() => {
        callback.current = onToken;
    }, [onToken]);

    useEffect(() => {
        let widgetId: string | null = null;
        let ativo = true;

        carregarScript()
            .then(() => {
                if (!ativo || !alvo.current || !window.turnstile) {
                    return;
                }

                widgetId = window.turnstile.render(alvo.current, {
                    sitekey: siteKey,
                    language: 'pt-br',
                    theme: 'light',
                    callback: (token) => callback.current(token),
                    'expired-callback': () => callback.current(''),
                    'error-callback': () => callback.current(''),
                });
            })
            .catch(() => callback.current(''));

        return () => {
            ativo = false;

            if (widgetId && window.turnstile) {
                window.turnstile.remove(widgetId);
            }
        };
    }, [siteKey]);

    return <div ref={alvo} />;
}
