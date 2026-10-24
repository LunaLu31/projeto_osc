import { route } from 'ziggy-js';
import { usePropsPagina } from '@/lib/pagina';

/**
 * Diz se a página atual corresponde a algum padrão de rota (ex.: 'osc.demandas.*'), para marcar
 * a aba atual dos menus. Usa a URL da própria página (prop `ziggy.location`, renovada a cada visita),
 * e não `window.location`: o resultado é o mesmo no SSR e já vale no render da página nova.
 */
export function useRotaAtual(): (...padroes: string[]) => boolean {
    const { ziggy } = usePropsPagina();
    const rotas = route(undefined, undefined, undefined, { ...ziggy, location: new URL(ziggy.location) });

    return (...padroes) => padroes.some((padrao) => rotas.current(padrao));
}
