import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

/**
 * Indica que a própria página está sendo recarregada (filtros, paginação, abas),
 * para exibir skeletons no lugar dos dados. Navegações para outras páginas não contam.
 * O aviso só aparece após um pequeno atraso, evitando "piscar" em respostas rápidas.
 */
export function useRecarregandoPagina(atrasoMs = 150): boolean {
    const [carregando, setCarregando] = useState(false);

    useEffect(() => {
        let espera: ReturnType<typeof setTimeout> | undefined;

        const removerInicio = router.on('start', (evento) => {
            if (evento.detail.visit.url.pathname === window.location.pathname) {
                espera = setTimeout(() => setCarregando(true), atrasoMs);
            }
        });
        const removerFim = router.on('finish', () => {
            clearTimeout(espera);
            setCarregando(false);
        });

        return () => {
            clearTimeout(espera);
            removerInicio();
            removerFim();
        };
    }, [atrasoMs]);

    return carregando;
}
